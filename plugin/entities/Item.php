<?php

namespace luckywp\wikiLinking\plugin\entities;

use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;
use WP_Post;

/**
 * @property bool $isCustom
 * @property bool $isPost
 * @property WP_Post $post
 * @property WP_Post[] $linkedPosts
 * @property bool $anchorChanged
 */
class Item extends BaseObject
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $typeId;

    /**
     * @var string
     */
    public $anchor;

    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $postId;

    public function getIsCustom()
    {
        return $this->typeId == ItemType::CUSTOM;
    }

    public function getIsPost()
    {
        return $this->typeId == ItemType::POST;
    }

    public function getPost()
    {
        return $this->isPost ? get_post($this->postId) : null;
    }

    public function generateUrl()
    {
        return $this->isCustom ? $this->url : get_permalink($this->postId);
    }

    private $_linkedPosts;

    /**
     * @return WP_Post[]
     */
    public function getLinkedPosts()
    {
        if ($this->_linkedPosts === null) {
            $this->_linkedPosts = Core::$plugin->posts->findPostsByItemId($this->id);
        }
        return $this->_linkedPosts;
    }

    public $oldAnchor;

    public function getAnchorChanged()
    {
        return $this->anchor !== $this->oldAnchor;
    }

    /**
     * @return bool
     */
    public function getIsNewRecord()
    {
        return $this->id === null;
    }

    public static function instantiate(array $row)
    {
        $item = new self();
        $item->id = (int)$row['id'];
        $item->typeId = (int)$row['type_id'];
        $item->anchor = $row['anchor'];
        $item->oldAnchor = $item->anchor;
        $item->url = $item->isCustom ? $row['url'] : null;
        $item->postId = $item->isPost ? (int)$row['post_id'] : null;
        return $item;
    }
}
