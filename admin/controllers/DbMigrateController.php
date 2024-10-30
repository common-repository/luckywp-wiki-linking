<?php

namespace luckywp\wikiLinking\admin\controllers;

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\admin\Controller;
use luckywp\wikiLinking\core\admin\helpers\AdminUrl;
use luckywp\wikiLinking\core\Core;

class DbMigrateController extends Controller
{

    public function actionIndex()
    {
        echo '<div class="wrap">';
        echo '<h1>LuckyWP Wiki Linking</h1>';
        echo '<p style="font-size:16px;line-height:22px;">' . esc_html__('Updating DB…', 'luckywp-wiki-linking') . ' ';
        Core::$plugin->db->migrate();
        echo '<b style="color:#00b10f">' . esc_html__('success', 'luckywp-wiki-linking') . '</b></p>';
        echo '<p>' . AdminHtml::button(esc_html__('Go to the plugin', 'luckywp-wiki-linking'), [
                'href' => AdminUrl::to('main'),
                'size' => AdminHtml::BUTTON_SIZE_LARGE,
            ]) . '</p>';
        echo '</div>';
    }
}
