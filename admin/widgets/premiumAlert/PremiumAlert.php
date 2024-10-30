<?php

namespace luckywp\wikiLinking\admin\widgets\premiumAlert;

use luckywp\wikiLinking\core\base\Widget;
use luckywp\wikiLinking\core\helpers\Html;

class PremiumAlert extends Widget
{

    /**
     * @var string
     */
    public $text;

    public $containerOptions = [];

    public function run()
    {
        $containerOptions = Html::prepareClassInOptions($this->containerOptions);
        $containerOptions['class'][] = 'lwpwlPremiumAlert';
        return $this->render('widget', [
            'text' => $this->text,
            'containerOptions' => $containerOptions,
        ]);
    }
}
