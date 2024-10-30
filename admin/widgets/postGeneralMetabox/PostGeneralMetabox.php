<?php

namespace luckywp\wikiLinking\admin\widgets\postGeneralMetabox;

use luckywp\wikiLinking\admin\widgets\processBinding\ProcessBinding;
use luckywp\wikiLinking\core\base\Widget;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\repositories\ItemFilter;
use WP_Post;

class PostGeneralMetabox extends Widget
{

    /**
     * @var WP_Post
     */
    public $post;

    /**
     * @var bool
     */
    public $onlyBody = false;

    public function run()
    {
        $filter = new ItemFilter();
        $filter->postId = $this->post->ID;
        $items = Core::$plugin->items->find($filter);

        $html = '';
        if (!$this->onlyBody) {
            $html .= '<div class="lwpwlmbPostGeneral" data-post-id="' . $this->post->ID . '">';
        }
        $html .= $this->render('box', [
            'post' => $this->post,
            'items' => $items,
        ], false);
        if (!$this->onlyBody) {
            $html .= '</div>';
            $html .= ProcessBinding::widget();
        }
        return $html;
    }
}
