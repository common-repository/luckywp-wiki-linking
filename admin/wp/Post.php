<?php

namespace luckywp\wikiLinking\admin\wp;

use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;
use WP_Post;

class Post extends BaseObject
{

    public function init()
    {
        add_action('delete_post', [$this, 'onDeletePost']);
        add_action('transition_post_status', [$this, 'onTransitionStatus'], 999, 3);
    }

    /**
     * @param int $postId
     */
    public function onDeletePost($postId)
    {
        Core::$plugin->items->deleteByPostId($postId);
        Core::$plugin->posts->deleteByPostId($postId);
    }

    /**
     * @param string $newStatus
     * @param string $oldStatus
     * @param WP_Post $post
     */
    public function onTransitionStatus($newStatus, $oldStatus, $post)
    {
        if ($newStatus != 'publish') {
            Core::$plugin->posts->deleteByPostId($post->ID);
        }
    }
}
