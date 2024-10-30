<?php

namespace luckywp\wikiLinking\plugin;

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\Core;

class Settings extends \luckywp\wikiLinking\core\wp\Settings
{

    public function init()
    {
        parent::init();
        add_action('init', [$this, 'addItemsPerPostFields'], 999);
    }

    public function addItemsPerPostFields()
    {
        foreach (Core::$plugin->postTypes as $postType) {
            $this->addField('items_per_' . $postType->name, 'general', 'items_per_post', [
                'label' => $postType->labels->singular_name,
                'widget' => 'textInput',
                'params' => [
                    'inputOptions' => [
                        'size' => AdminHtml::TEXT_INPUT_SIZE_SMALL,
                    ],
                ],
                'sanitizeCallback' => function ($value) {
                    $value = (int)$value;
                    if ($value < 0) {
                        $value = 0;
                    }
                    return $value;
                },
                'default' => $postType->name == 'post' ? 2 : 0,
            ]);
        }
    }
}
