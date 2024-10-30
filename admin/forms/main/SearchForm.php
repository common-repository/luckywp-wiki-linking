<?php

namespace luckywp\wikiLinking\admin\forms\main;

use luckywp\wikiLinking\core\base\Model;
use luckywp\wikiLinking\plugin\repositories\ItemFilter;

class SearchForm extends Model
{

    public $anchor;

    public $url;

    public function rules()
    {
        return [
            [['anchor', 'url'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @param ItemFilter $filter
     */
    public function toFilter($filter)
    {
        $filter->likeAnchor = empty($this->anchor) ? null : $this->anchor;
        $filter->searchByUrl = empty($this->url) ? null : $this->url;
    }
}
