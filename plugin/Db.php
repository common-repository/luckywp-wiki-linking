<?php

namespace luckywp\wikiLinking\plugin;

use luckywp\wikiLinking\core\Core;

class Db extends \luckywp\wikiLinking\core\db\Db
{

    const VERSION = 1;

    public function migrate()
    {
        $version = (int)Core::$plugin->options->get('dbVersion', 0);
        if (!$version) {
            $this->newDbStructure();
            Core::$plugin->options->set('dbVersion', Db::VERSION);
        } elseif ($version < Db::VERSION) {
            for ($i = $version + 1; $i <= Db::VERSION; $i++) {
                $callable = [$this, 'migrate' . $i];
                if (is_callable($callable)) {
                    call_user_func($callable);
                }
            }
            Core::$plugin->options->set('dbVersion', Db::VERSION);
        }
    }

    public function newDbStructure()
    {
        global $wpdb;
        $collate = $wpdb->get_charset_collate();
        $wpdb->query('CREATE TABLE ' . $wpdb->prefix . 'lwpwl_item (
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            type_id TINYINT UNSIGNED NOT NULL DEFAULT 1,
            anchor VARCHAR(255) NOT NULL,
            url VARCHAR(255),
            post_id BIGINT(20) UNSIGNED,
            UNIQUE KEY id (id)
            ) ' . $collate . ' AUTO_INCREMENT=1');
        $wpdb->query(' CREATE TABLE ' . $wpdb->prefix . 'lwpwl_post (
            post_id INT NOT NULL,
            item_id INT NOT NULL
            ) ' . $collate);
    }
}
