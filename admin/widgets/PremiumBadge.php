<?php

namespace luckywp\wikiLinking\admin\widgets;

use luckywp\wikiLinking\core\base\Widget;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\helpers\Html;

class PremiumBadge extends Widget
{

    public function run()
    {
        return Html::tag(
            Core::$plugin->buyUrl ? 'a' : 'span',
            esc_html__('Premium', 'luckywp-wiki-linking'),
            [
                'class' => 'lwpwlPremiumBadge',
                'title' => __('It is available only in the premium version', 'luckywp-wiki-linking'),
                'href' => Core::$plugin->buyUrl ? Core::$plugin->buyUrl : null,
                'target' => Core::$plugin->buyUrl ? '_blank' : null,
            ]
        );
    }
}
