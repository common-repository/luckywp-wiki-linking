<?php

namespace luckywp\wikiLinking\plugin\repositories;

use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\helpers\ArrayHelper;
use WP_Post;
use wpdb;

/**
 * @property string $tableName
 */
class PostRepository extends BaseObject
{

    /**
     * @var wpdb
     */
    public $wpdb;

    public function init()
    {
        $this->wpdb = Core::$plugin->db->wpdb;
    }

    public function getTableName()
    {
        return Core::$plugin->db->prefix . 'post';
    }

    /**
     * @param int $id
     * @return int
     */
    public function countItemsByPostId($id)
    {
        return (int)$this->wpdb->get_var('SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE post_id=' . $id);
    }

    /**
     * @param int $itemId
     * @return int[]
     */
    public function findPostIdsByItemId($itemId)
    {
        $rows = $this->wpdb->get_col('SELECT post_id FROM ' . $this->tableName . ' WHERE item_id=' . $itemId);
        return array_map('intval', $rows);
    }

    /**
     * @param int $itemId
     * @return WP_Post[]
     */
    public function findPostsByItemId($itemId)
    {
        $postIds = $this->findPostIdsByItemId($itemId);
        if ($postIds) {
            return get_posts([
                'nopaging' => true,
                'post__in' => $postIds,
                'post_type' => Core::$plugin->getPostTypeNames(),
            ]);
        }
        return [];
    }

    /**
     * @param int $itemId
     * @return array
     */
    public function findPostContentsToAdd($itemId)
    {
        $itemsPerPost = Core::$plugin->getItemsPerPost();
        if (!$itemsPerPost) {
            return [];
        }

        $sql = 'SELECT p.ID, p.post_content FROM ' . $this->wpdb->posts . ' AS p' .
            ' LEFT JOIN (SELECT post_id, COUNT(*) AS cnt FROM ' . $this->tableName . ' GROUP BY post_id) AS c ON c.post_id=p.ID' .
            ' WHERE p.post_status="publish"';

        $conditions = [];
        foreach ($itemsPerPost as $postTypeName => $perPost) {
            $conditions[] = '(p.post_type="' . $postTypeName . '" AND (c.cnt IS NULL OR c.cnt<' . $perPost . '))';
        }
        $sql .= ' AND (' . implode(' OR ', $conditions) . ')';

        $sql .= ' AND p.ID NOT IN (SELECT DISTINCT post_id FROM ' . $this->tableName . ' WHERE item_id=' . $itemId . ')';

        return ArrayHelper::map($this->wpdb->get_results($sql, ARRAY_A), 'ID', 'post_content');
    }

    /**
     * @param int $postId
     * @param int $itemId
     */
    public function create($postId, $itemId)
    {
        $this->wpdb->insert($this->tableName, ['post_id' => $postId, 'item_id' => $itemId]);
    }


    /**
     * ---------------------------------------------------------------------------
     *  Удаление записи
     * ---------------------------------------------------------------------------
     */

    /**
     * @param array $where
     */
    private function deleteRows($where)
    {
        $this->wpdb->delete($this->tableName, $where);
    }

    public function delete($postId, $itemId)
    {
        $this->deleteRows([
            'post_id' => $postId,
            'item_id' => $itemId,
        ]);
    }

    public function deleteByPostId($postId)
    {
        $this->deleteRows(['post_id' => $postId]);
    }

    public function deleteByItemId($itemId)
    {
        $this->deleteRows(['item_id' => $itemId]);
    }
}
