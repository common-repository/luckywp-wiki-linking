<?php

namespace luckywp\wikiLinking\admin\forms\main;

use luckywp\wikiLinking\core\base\Model;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\dto\CreateItemDto;
use luckywp\wikiLinking\plugin\entities\ItemType;
use luckywp\wikiLinking\plugin\repositories\ItemFilter;

class CreateForm extends Model
{

    /**
     * @var string
     */
    public $anchor;

    /**
     * @var int
     */
    public $typeId = ItemType::CUSTOM;

    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $postId;

    public function rules()
    {
        return [
            ['anchor', 'filter', 'filter' => 'trim'],
            ['anchor', 'required'],
            [
                'anchor',
                function () {
                    $filter = new ItemFilter();
                    $filter->anchor = $this->anchor;
                    if (Core::$plugin->items->exists($filter)) {
                        $this->addError('anchor', esc_html__('Keyword phrase already exists', 'luckywp-wiki-linking'));
                    }
                    if (Core::$plugin->items->count() >= 30) {
                        $this->addError('anchor', esc_html__('Adding more than 30 keyword phrases is available in the premium version', 'luckywp-wiki-linking'));
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

    /**
     * @return CreateItemDto
     */
    public function makeDto()
    {
        $dto = new CreateItemDto();
        $dto->typeId = (int)$this->typeId;
        $dto->anchor = $this->anchor;
        $dto->url = $this->url;
        $dto->postId = (int)$this->postId;
        return $dto;
    }
}
