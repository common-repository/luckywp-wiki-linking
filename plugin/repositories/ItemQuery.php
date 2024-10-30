<?php

namespace luckywp\wikiLinking\plugin\repositories;

use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\entities\Item;
use luckywp\wikiLinking\plugin\entities\ItemType;
use wpdb;

class ItemQuery extends BaseObject
{

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var wpdb
     */
    protected $wpdb;

    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var string
     */
    protected $orderBy;

    public function init()
    {
        $this->tableName = Core::$plugin->items->tableName;
        $this->wpdb = Core::$plugin->db->wpdb;
        $this->select = [
            $this->tableName . '.id',
            $this->tableName . '.type_id',
            $this->tableName . '.anchor',
            $this->tableName . '.url',
            $this->tableName . '.post_id',
        ];
    }

    /**
     * @param string $condition
     * @return self
     */
    public function addCondition($condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @param ItemFilter|null $filter
     * @return self
     */
    public function applyFilter($filter = null)
    {
        if ($filter) {
            if ($filter->id) {
                $this->addCondition($this->tableName . '.id=' . $filter->id);
            }
            if ($filter->notId) {
                $this->addCondition($this->tableName . '.id!=' . $filter->notId);
            }
            if ($filter->typeId) {
                $this->addCondition($this->tableName . '.type_id=' . $filter->typeId);
            }
            if ($filter->anchor) {
                $this->addCondition($this->tableName . '.anchor="' . esc_sql($filter->anchor) . '"');
            }
            if ($filter->likeAnchor) {
                $this->addCondition($this->tableName . '.anchor LIKE "%' . $this->wpdb->esc_like($filter->likeAnchor) . '%"');
            }
            if ($filter->likeUrl) {
                $this->addCondition($this->tableName . '.url LIKE "%' . $this->wpdb->esc_like($filter->likeUrl) . '%"');
            }
            if ($filter->postId) {
                $this->addCondition($this->tableName . '.post_id=' . $filter->postId);
            }
            if ($filter->notPostId) {
                $this->addCondition($this->tableName . '.post_id!=' . $filter->notPostId);
            }

            // Поиск по ссылке
            if ($filter->searchByUrl) {
                $sql = '(';

                // Произвольный URL
                $sql .= '(';
                $sql .= $this->tableName . '.type_id=' . ItemType::CUSTOM;
                $sql .= ' AND ' . $this->tableName . '.url LIKE "%' . $this->wpdb->esc_like($filter->searchByUrl) . '%"';
                $sql .= ')';

                // ID поста
                if (intval($filter->searchByUrl) > 0) {
                    $sql .= ' OR (';
                    $sql .= $this->tableName . '.type_id=' . ItemType::POST;
                    $sql .= ' AND ' . $this->tableName . '.post_id=' . intval($filter->searchByUrl);
                    $sql .= ')';
                }

                // Название поста
                $this->leftJoinPosts();
                $sql .= ' OR (';
                $sql .= $this->tableName . '.type_id=' . ItemType::POST;
                $sql .= ' AND wpp.post_title LIKE "%' . $this->wpdb->esc_like($filter->searchByUrl) . '%"';
                $sql .= ')';

                $sql .= ')';
                $this->addCondition($sql);
            }

            if ($filter->linkedPostId) {
                $sql = 'SELECT DISTINCT item_id FROM ' . Core::$plugin->posts->tableName . ' WHERE post_id=' . $filter->linkedPostId;
                $this->addCondition($this->tableName . '.id IN (' . $sql . ')');
            }
            if ($filter->notLinkedPostId) {
                $sql = 'SELECT DISTINCT item_id FROM ' . Core::$plugin->posts->tableName . ' WHERE post_id=' . $filter->notLinkedPostId;
                $this->addCondition($this->tableName . '.id NOT IN (' . $sql . ')');
            }
            if ($filter->lessCountLinkedPosts) {
                $this->leftJoinCountLinkedPosts();
                $this->addCondition('(tbl_count_posts.item_id IS NULL OR tbl_count_posts.cnt<' . $filter->lessCountLinkedPosts . ')');
            }
            if ($filter->perPage) {
                $this->limit = $filter->perPage;
                if ($filter->page > 1) {
                    $this->offset = (($filter->page - 1) * $filter->perPage);
                }
            }
        }
        return $this;
    }

    protected $_joins = [];

    public function leftJoinCountLinkedPosts()
    {
        if (!in_array('countLinkedPosts', $this->_joins)) {
            $this->_joins[] = 'countLinkedPosts';
            $sql = 'SELECT item_id, COUNT(*) AS cnt FROM ' . Core::$plugin->posts->tableName . ' GROUP BY item_id';
            $this->joins[] = 'LEFT JOIN (' . $sql . ') AS tbl_count_posts ON tbl_count_posts.item_id=' . $this->tableName . '.id';
        }
        return $this;
    }

    public function leftJoinPosts()
    {
        if (!in_array('posts', $this->_joins)) {
            $this->_joins[] = 'posts';
            $this->joins[] = 'LEFT JOIN ' . $this->wpdb->posts . ' AS wpp ON wpp.ID=' . $this->tableName . '.post_id';
        }
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        $this->select = ['COUNT(*)'];
        return $this->wpdb->get_var($this->makeSql());
    }

    /**
     * @return bool
     */
    public function exists()
    {
        $this->select = [$this->tableName . '.id'];
        $this->limit = 1;
        return (bool)$this->wpdb->get_var($this->makeSql());
    }

    /**
     * @return Item[]
     */
    public function find()
    {
        $this->orderBy = 'id DESC';
        $rows = $this->wpdb->get_results($this->makeSql(), ARRAY_A);

        $items = [];
        foreach ($rows as $row) {
            $items[] = Item::instantiate($row);
        }

        return $items;
    }

    /**
     * @return string
     */
    protected function makeSql()
    {
        $sql = 'SELECT ' . implode(',', $this->select);
        $sql .= ' FROM ' . $this->tableName;
        if ($this->joins) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
        if ($this->conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }
        if ($this->orderBy) {
            $sql .= ' ORDER BY ' . $this->orderBy;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }
        return $sql;
    }
}
