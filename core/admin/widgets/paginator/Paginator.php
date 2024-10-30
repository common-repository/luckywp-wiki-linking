<?php

namespace luckywp\wikiLinking\core\admin\widgets\paginator;

use luckywp\wikiLinking\core\base\Widget;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\data\PaginationData;
use luckywp\wikiLinking\core\helpers\Html;

class Paginator extends Widget
{

    /**
     * @var PaginationData
     */
    public $data;

    /**
     * @var string
     */
    public $classPrefix;

    /**
     * @var string
     */
    public $containerClass;

    /**
     * @var string
     */
    public $before;

    /**
     * @var string
     */
    public $after;

    /**
     * @var array
     */
    public $url;

    /**
     * @var string
     */
    public $queryArg = 'p';

    public function init()
    {
        if ($this->classPrefix === null) {
            $this->classPrefix = rtrim(Core::$plugin->prefix, '_');
        }
    }

    public function run()
    {
        if ($this->data->countPages == 1) {
            return '';
        }

        $containerOptions = [
            'class' => [$this->classPrefix . 'Paginator']
        ];
        if ($this->containerClass) {
            $containerOptions['class'][] = $this->containerClass;
        }

        $html = $this->before . Html::beginTag('div', $containerOptions);

        $begin = $this->data->page - 10;
        if ($begin < 1) {
            $begin = 1;
        }
        $end = $this->data->page + 10;
        if ($end > $this->data->countPages) {
            $end = $this->data->countPages;
        }
        for ($i = $begin; $i <= $end; $i++) {
            $options = [];
            if ($i == $this->data->page) {
                $options['class'] = $this->classPrefix . 'Paginator_current';
            }
            $html .= Html::a($i, add_query_arg($this->queryArg, $i), $options);
        }

        $html .= '</div>' . $this->after;

        return $html;
    }
}
