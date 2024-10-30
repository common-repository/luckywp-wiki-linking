<?php

namespace luckywp\wikiLinking\core\admin\helpers;

use luckywp\wikiLinking\core\Core;

class AdminUrl
{

    /**
     * @param string $pageId
     * @param string|null $action
     * @param array $params
     * @return string
     */
    public static function to($pageId, $action = null, $params = [])
    {
        $params['page'] = Core::$plugin->prefix . $pageId;
        if ($action !== null) {
            $params['action'] = $action;
        }
        return admin_url('admin.php?' . http_build_query($params));
    }

    /**
     * @param string $pageId
     * @return bool
     */
    public static function isPage($pageId)
    {
        return Core::$plugin->request->get('page') == Core::$plugin->prefix . $pageId;
    }
}
