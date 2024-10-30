<?php

namespace luckywp\wikiLinking\core\db;

use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;
use wpdb;

class Db extends BaseObject
{

    /**
     * @var wpdb
     */
    public $wpdb;

    public $prefix;

    public function init()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        if ($this->prefix === null) {
            $this->prefix = $wpdb->prefix . Core::$plugin->prefix;
        }
    }
}
