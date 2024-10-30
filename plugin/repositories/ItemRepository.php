<?php

namespace luckywp\wikiLinking\plugin\repositories;

use Exception;
use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\dto\CreateItemDto;
use luckywp\wikiLinking\plugin\entities\Item;
use luckywp\wikiLinking\plugin\entities\ItemType;
use wpdb;

/**
 * @property string $tableName
 */
class ItemRepository extends BaseObject
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
        return Core::$plugin->db->prefix . 'item';
    }

    /**
     * @return ItemQuery
     */
    protected function makeQuery()
    {
        /** @var ItemQuery $query */
        $query = Core::createObject(ItemQuery::className());
        return $query;
    }

    /**
     * @param int $id
     * @return Item|null
     */
    public function get($id)
    {
        $row = $this->wpdb->get_row('SELECT * FROM ' . $this->tableName . ' WHERE id=' . $id, ARRAY_A);
        return $row ? Item::instantiate($row) : null;
    }

    /**
     * @param ItemFilter null $filter
     * @return int
     */
    public function count($filter = null)
    {
        return $this->makeQuery()->applyFilter($filter)->count();
    }

    /**
     * @param ItemFilter|null $filter
     * @return bool
     */
    public function exists($filter = null)
    {
        return $this->makeQuery()->applyFilter($filter)->exists();
    }

    /**
     * @param ItemFilter $filter
     * @return Item[]
     */
    public function find($filter)
    {
        return $this->makeQuery()->applyFilter($filter)->find();
    }

    /**
     * @param CreateItemDto $dto
     * @return Item
     */
    public function create($dto)
    {
        if ($this->count() >= 30) {
            throw new Exception('Adding more than 30 keyword phrases is available in the premium version');
        }
        $item = new Item();
        $item->typeId = $dto->typeId;
        $item->anchor = $dto->anchor;
        if ($dto->typeId === ItemType::CUSTOM) {
            $item->url = $dto->url;
        }
        if ($dto->typeId === ItemType::POST) {
            $item->postId = $dto->postId;
        }
        $this->save($item);
        return $item;
    }

    /**
     * Добавить ключевые фразы для поста
     * @param int $postId
     * @param string[] $keywords
     * @return Item[]
     */
    public function addKeywordsToPost($postId, $keywords)
    {
        $items = [];
        $dto = new CreateItemDto();
        $dto->typeId = ItemType::POST;
        $dto->postId = $postId;
        foreach ($keywords as $keyword) {
            $dto->anchor = $keyword;
            $items[] = $this->create($dto);
        }
        return $items;
    }

    /**
     * @param Item $item
     */
    public function save($item)
    {
        $data = [
            'type_id' => $item->typeId,
            'anchor' => $item->anchor,
            'url' => $item->url,
            'post_id' => $item->postId,
        ];
        if ($item->anchorChanged && !$item->getIsNewRecord()) {
            Core::$plugin->posts->deleteByItemId($item->id);
        }
        if ($item->id) {
            $this->wpdb->update($this->tableName, $data, ['id' => $item->id]);
        } else {
            $this->wpdb->insert($this->tableName, $data);
            $item->id = $this->wpdb->insert_id;
        }
    }


    /**
     * ---------------------------------------------------------------------------
     *  Удаление записей
     * ---------------------------------------------------------------------------
     */

    /**
     * @param Item $item
     */
    public function delete($item)
    {
        $this->wpdb->delete($this->tableName, ['id' => $item->id]);
        Core::$plugin->posts->deleteByItemId($item->id);
    }

    /**
     * @param int $postId
     */
    public function deleteByPostId($postId)
    {
        $itemIds = $this->wpdb->get_col('SELECT id FROM ' . $this->tableName . ' WHERE post_id=' . $postId);
        foreach ($itemIds as $itemId) {
            Core::$plugin->posts->deleteByItemId($itemId);
        }
        $this->wpdb->delete($this->tableName, ['post_id' => $postId]);
    }
}
