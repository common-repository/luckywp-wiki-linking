<?php

namespace luckywp\wikiLinking\core\data;

use luckywp\wikiLinking\core\base\BaseObject;

/**
 * @property int $countPages
 */
class PaginationData extends BaseObject
{

    /**
     * @var int Текущая страница
     */
    public $page;

    /**
     * @var int Количество элементов на странице
     */
    public $perPage;

    /**
     * @var int Количество элементов
     */
    public $count;

    /**
     * @var int Количество страниц
     */
    private $_countPages;

    public function setCountPages($value)
    {
        $this->_countPages = $value;
    }

    public function getCountPages()
    {
        if ($this->_countPages === null) {
            $this->_countPages = ceil($this->count / $this->perPage);
        }
        return $this->_countPages;
    }

    public function isValidPage()
    {
        return $this->page >= 1 && $this->page <= $this->countPages;
    }

    public function sanitizePage()
    {
        if (!$this->isValidPage()) {
            $this->page = 1;
        }
    }
}
