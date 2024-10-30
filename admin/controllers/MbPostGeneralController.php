<?php

namespace luckywp\wikiLinking\admin\controllers;

use luckywp\wikiLinking\admin\forms\postKeyword\CreateForm;
use luckywp\wikiLinking\admin\forms\postKeyword\UpdateForm;
use luckywp\wikiLinking\admin\widgets\postGeneralMetabox\PostGeneralMetabox;
use luckywp\wikiLinking\admin\widgets\premiumAlert\PremiumAlert;
use luckywp\wikiLinking\core\admin\Controller;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\entities\Item;
use luckywp\wikiLinking\plugin\repositories\ItemRepository;

class MbPostGeneralController extends Controller
{

    /**
     * @var ItemRepository
     */
    protected $items;

    public function init()
    {
        $this->items = Core::$plugin->items;
        parent::init();
        add_action('plugins_loaded', [$this, 'initAjax']);
    }

    public function initAjax()
    {
        if (current_user_can('manage_options')) {
            add_action('wp_ajax_lwpwlmb_post_general_html', [$this, 'ajaxHtml']);
            add_action('wp_ajax_lwpwlmb_post_general_keyword_view', [$this, 'ajaxKeywordView']);
            add_action('wp_ajax_lwpwlmb_post_general_keyword_add', [$this, 'ajaxKeywordAdd']);
            add_action('wp_ajax_lwpwlmb_post_general_keyword_update', [$this, 'ajaxKeywordUpdate']);
        }
    }

    /**
     * Содержимое метабокса
     */
    public function ajaxHtml()
    {
        $post = get_post((int)Core::$plugin->request->get('postId'));
        if (!$post) {
            $this->notAllowed();
        }
        echo PostGeneralMetabox::widget([
            'post' => $post,
            'onlyBody' => true,
        ]);
        wp_die();
    }

    /**
     * @return Item
     */
    protected function getItem()
    {
        $item = $this->items->get((int)Core::$plugin->request->get('id'));
        if ($item === null || !$item->isPost) {
            $this->notAllowed();
        }
        return $item;
    }

    public function ajaxKeywordView()
    {
        $this->render('keyword_view', [
            'item' => $this->getItem(),
        ]);
        wp_die();
    }

    /**
     * Добавление ключевых фраз
     */
    public function ajaxKeywordAdd()
    {
        if ($this->items->count() >= 30) {
            PremiumAlert::widget([
                'text' => esc_html__('Adding more than 30 keyword phrases is available in the premium version', 'luckywp-wiki-linking'),
            ]);
            wp_die();
        }

        $post = get_post((int)Core::$plugin->request->get('postId'));
        if (!$post) {
            $this->notAllowed();
        }

        $model = new CreateForm();
        if ($model->load(Core::$plugin->request->post()) && $model->validate()) {
            $this->items->addKeywordsToPost($post->ID, $model->getKeywordsArray());
            $this->render('keyword_add_success');
            wp_die();
        }

        $this->render('keyword_add', [
            'post' => $post,
            'model' => $model,
        ]);
        wp_die();
    }

    /**
     * Редактирование ключевой фразы
     */
    public function ajaxKeywordUpdate()
    {
        $item = $this->items->get((int)Core::$plugin->request->get('id'));
        if (!$item) {
            $this->notAllowed();
        }

        $model = new UpdateForm($item);
        if ($model->load(Core::$plugin->request->post()) && $model->validate()) {
            $model->toItem($item);
            $this->items->save($item);
            $this->render('keyword_update_success');
            wp_die();
        }

        $this->render('keyword_update', [
            'model' => $model,
        ]);
        wp_die();
    }
}
