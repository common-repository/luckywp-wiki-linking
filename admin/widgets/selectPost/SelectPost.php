<?php

namespace luckywp\wikiLinking\admin\widgets\selectPost;

use luckywp\wikiLinking\core\base\Model;
use luckywp\wikiLinking\core\base\Widget;
use luckywp\wikiLinking\core\helpers\Html;

class SelectPost extends Widget
{

    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    public function run()
    {
        $postId = (int)$this->model->{$this->attribute};
        if ($postId < 1) {
            $postId = null;
        }

        $post = $postId ? get_post($postId) : null;

        return $this->render('widget', [
            'name' => Html::getInputName($this->model, $this->attribute),
            'post' => $post,
        ]);
    }
}
