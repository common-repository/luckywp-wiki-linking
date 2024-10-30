<?php

namespace luckywp\wikiLinking\admin\forms\postKeyword;

use luckywp\wikiLinking\core\base\Model;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\entities\Item;
use luckywp\wikiLinking\plugin\repositories\ItemFilter;

/**
 * @var Item $item
 */
class UpdateForm extends Model
{

    /**
     * @var string
     */
    public $anchor;

    /**
     * @var Item
     */
    private $_item;

    /**
     * @param Item $item
     * @param array $config
     */
    public function __construct($item, array $config = [])
    {
        $this->anchor = $item->anchor;
        $this->_item = $item;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['anchor', 'filter', 'filter' => 'trim'],
            ['anchor', 'required'],
            [
                'anchor',
                function () {
                    $filter = new ItemFilter();
                    $filter->notId = $this->_item->id;
                    $filter->anchor = $this->anchor;
                    if (Core::$plugin->items->exists($filter)) {
                        $this->addError('anchor', esc_html__('Keyword phrase already exists', 'luckywp-wiki-linking'));
                    }
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'anchor' => __('Keyword phrase', 'luckywp-wiki-linking'),
        ];
    }

    public function getItem()
    {
        return $this->_item;
    }

    /**
     * @param Item $item
     */
    public function toItem($item)
    {
        $item->anchor = $this->anchor;
    }
}
