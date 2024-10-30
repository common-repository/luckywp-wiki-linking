<?php

namespace luckywp\wikiLinking\plugin;

use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;

class Activation extends BaseObject
{

    public function init()
    {
        register_activation_hook(Core::$plugin->fileName, [$this, 'activate']);
    }

    public function activate()
    {
        Core::$plugin->db->migrate();
        Core::$plugin->settings->addItemsPerPostFields();
        Core::$plugin->settings->install();
    }
}
