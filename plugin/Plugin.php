<?php

namespace luckywp\wikiLinking\plugin;

use luckywp\wikiLinking\admin\Admin;
use luckywp\wikiLinking\core\base\BasePlugin;
use luckywp\wikiLinking\core\base\Request;
use luckywp\wikiLinking\core\base\View;
use luckywp\wikiLinking\core\helpers\ArrayHelper;
use luckywp\wikiLinking\core\wp\Options;
use luckywp\wikiLinking\plugin\entities\Item;
use luckywp\wikiLinking\plugin\repositories\ItemRepository;
use luckywp\wikiLinking\plugin\repositories\PostRepository;
use WP_Post_Type;

/**
 * @property Settings $settings
 * @property Options $options
 * @property Db $db
 * @property Request $request
 * @property View $view
 *
 * @property Admin $admin
 * @property ItemRepository $items
 * @property PostRepository $posts
 *
 * @property WP_Post_Type[] $postTypes
 */
class Plugin extends BasePlugin
{

    /**
     * @var bool Активен ли плагин?
     */
    public $active = true;

    /**
     * @var string Ссылка на покупку плагина
     */
    public $buyUrl = 'https://theluckywp.com/product/wiki-linking/?utm_source=free';

    /**
     * @param self $plugin
     */
    public static function checkDb($plugin)
    {
        if ($plugin->options->get('dbVersion') != Db::VERSION) {
            $plugin->active = false;
        };
    }

    /**
     * Количество ссылок на один пост
     * @param string|null $name Имя типа поста
     * @return int|array
     */
    public function getItemsPerPost($name = null)
    {
        if ($name === null) {
            $result = [];
            foreach ($this->postTypes as $postType) {
                $perPost = $this->getItemsPerPost($postType->name);
                if ($perPost) {
                    $result[$postType->name] = $perPost;
                }
            }
            return $result;
        }
        return (int)$this->settings->getValue('general', 'items_per_' . $name, 0);
    }

    private $_postTypes;

    /**
     * @return WP_Post_Type[]
     */
    public function getPostTypes()
    {
        if ($this->_postTypes === null) {
            $this->_postTypes = get_post_types([
                'public' => true,
            ], 'objects');
        }
        return $this->_postTypes;
    }

    /**
     * @return string[]
     */
    public function getPostTypeNames()
    {
        return ArrayHelper::getColumn($this->getPostTypes(), 'name');
    }

    /**
     * Привязать ключевую фразу к постам
     * @param Item $item
     * @return int
     */
    public function bindItemToPosts($item)
    {
        $postsPerItem = $this->settings->getValue('general', 'posts_per_item');

        $countPosts = count($item->linkedPosts);
        if ($countPosts >= $postsPerItem) {
            return $countPosts;
        }

        $postsData = $this->posts->findPostContentsToAdd($item->id);

        // Исключим сам пост, на который будут вести ссылки
        if ($item->isPost) {
            unset($postsData[$item->postId]);
        }

        foreach ($postsData as $postId => $postContent) {
            if ($this->addLink2Text($item->anchor, $item->url, $postContent)) {
                $this->posts->create($postId, $item->id);
                $countPosts++;
                if ($countPosts == $postsPerItem) {
                    return $countPosts;
                }
            }
        }

        return $countPosts;
    }

    /**
     * Добавляет ссылку с заданным анкором и URL в текст
     * @param string $anchor
     * @param string $url
     * @param string $text
     * @return bool
     */
    public function addLink2Text($anchor, $url, &$text)
    {
        $exclude_tags = 'a|pre|code|kbd|xmp|h1|h2|h3|h4|h5|h6';
        $search = str_replace('/', '\/', $anchor);
        $search_regex = "/(?<!\pL)($search)(?!\pL)(?!(?:(?!<\/?(?:$exclude_tags).*?>).)*<\/(?:$exclude_tags).*?>)(?![^<>]*>)/uimsU";
        $text = preg_replace($search_regex, '<a href="' . $url . '">$0</a>', $text, 1, $count);
        return $count > 0;
    }

    private function pluginI18n()
    {
        __('The plugin allows to easily organize a linking in the wiki style on the website.', 'luckywp-wiki-linking');
    }
}
