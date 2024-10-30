<?php

namespace luckywp\wikiLinking\admin\forms\main;

use luckywp\wikiLinking\core\base\Model;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\entities\Item;
use luckywp\wikiLinking\plugin\entities\ItemType;
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
     * @var int
     */
    public $typeId;

    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $postId;

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
        $this->typeId = $item->typeId;
        $this->anchor = $item->anchor;
        $this->url = $item->url;
        $this->postId = $item->postId;
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
            ['typeId', 'required'],
            ['typeId', 'in', 'range' => ItemType::toIds()],
            [
                'url',
                'filter',
                'filter' => 'trim',
                'when' => function () {
                    return $this->typeId == ItemType::CUSTOM;
                }
            ],
            [
                'url',
                'required',
                'when' => function () {
                    return $this->typeId == ItemType::CUSTOM;
                }
            ],
            [
                'url',
                'url',
                'when' => function () {
                    return $this->typeId == ItemType::CUSTOM;
                }
            ],
            [
                'postId',
                'required',
                'when' => function () {
                    return $this->typeId == ItemType::POST;
                }
            ],
            [
                'postId',
                [$this, 'isPost'],
                'when' => function () {
                    return $this->typeId == ItemType::POST;
                }
            ]
        ];
    }

    public function isPost()
    {
        $post = get_post((int)$this->postId);
        if ($post === null || !get_post_type_object($post->post_type)->public) {
            $this->addError('postId', esc_html__('Wrong post is selected', 'luckywp-wiki-linking'));
        }
    }

    public function attributeLabels()
    {
        return [
            'anchor' => __('Keyword phrase', 'luckywp-wiki-linking'),
            'typeId' => __('Link type', 'luckywp-wiki-linking'),
            'url' => __('Link', 'luckywp-wiki-linking'),
            'postId' => __('Post', 'luckywp-wiki-linking'),
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
        $item->typeId = $this->typeId;
        $item->anchor = $this->anchor;
        $item->url = null;
        $item->postId = null;
        if ($item->isCustom) {
            $item->url = $this->url;
        }
        if ($item->isPost) {
            $item->postId = $this->postId;
        }
    }
}
