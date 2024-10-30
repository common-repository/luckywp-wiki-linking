<?php

namespace luckywp\wikiLinking\admin;

use luckywp\wikiLinking\admin\controllers\DbMigrateController;
use luckywp\wikiLinking\admin\controllers\MainController;
use luckywp\wikiLinking\admin\controllers\MbPostGeneralController;
use luckywp\wikiLinking\admin\controllers\SettingsController;
use luckywp\wikiLinking\admin\widgets\noticeDbMigrate\NoticeDbMigrate;
use luckywp\wikiLinking\admin\widgets\postGeneralMetabox\PostGeneralMetabox;
use luckywp\wikiLinking\admin\wp\Post;
use luckywp\wikiLinking\core\admin\helpers\AdminUrl;
use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\helpers\ArrayHelper;
use luckywp\wikiLinking\core\helpers\Html;

/**
 * @property string $assetsUrl
 */
class Admin extends BaseObject
{

    /**
     * @var Post
     */
    public $wpPost;

    /**
     * @var string
     */
    public $pageSettingsHook;

    public function init()
    {
        if (is_admin()) {
            add_action('admin_menu', [$this, 'menu']);

            // Ссылки в списке плагинов
            add_filter('plugin_action_links_' . Core::$plugin->basename, function ($links) {
                $links[] = Html::a(esc_html__('Settings', 'luckywp-wiki-linking'), AdminUrl::to('settings'));
                $links[] = Html::a(esc_html__('Get Premium', 'luckywp-wiki-linking'), Core::$plugin->buyUrl, ['target' => '_blank']);
                return $links;
            });

            if (Core::$plugin->active) {
                if (!wp_doing_ajax()) {
                    add_action('admin_enqueue_scripts', [$this, 'assets']);
                }
                $this->wpPost = Core::createObject(Post::className());
                add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
                MainController::getInstance();
                MbPostGeneralController::getInstance();
            } else {
                if (!AdminUrl::isPage('dbMigrate')) {
                    add_action('admin_notices', function () {
                        echo NoticeDbMigrate::widget();
                    });
                }
                DbMigrateController::getInstance();
            }
        }
    }

    public function addMetaBoxes()
    {
        if (current_user_can('manage_options')) {
            add_meta_box(
                Core::$plugin->prefix . '_postGeneral',
                esc_html__('Wiki Linking', 'luckywp-wiki-linking'),
                function ($post) {
                    echo PostGeneralMetabox::widget([
                        'post' => $post,
                    ]);
                },
                ArrayHelper::getColumn(Core::$plugin->getPostTypes(), 'name'),
                'normal',
                'high'
            );
        }
    }

    public function getAssetsUrl()
    {
        return Core::$plugin->url . '/admin/assets';
    }

    public static function menu()
    {
        if (Core::$plugin->active) {
            add_menu_page(
                esc_html__('Wiki Linking', 'luckywp-wiki-linking'),
                esc_html__('Wiki Linking', 'luckywp-wiki-linking'),
                'manage_options',
                Core::$plugin->prefix . 'main',
                [MainController::className(), 'router'],
                'dashicons-randomize',
                81
            );
            Core::$plugin->admin->pageSettingsHook = add_submenu_page(
                Core::$plugin->prefix . 'main',
                esc_html__('Wiki Linking Settings', 'luckywp-wiki-linking'),
                esc_html__('Settings', 'luckywp-wiki-linking'),
                'manage_options',
                Core::$plugin->prefix . 'settings',
                [SettingsController::className(), 'router']
            );
        } else {
            add_submenu_page(
                'index.php?lwp',
                'LuckyWP Wiki Linking — ' . esc_html__('Updating Database', 'luckywp-wiki-linking'),
                esc_html__('Updating Database', 'luckywp-wiki-linking'),
                'manage_options',
                Core::$plugin->prefix . 'dbMigrate',
                [DbMigrateController::className(), 'router']
            );
        }
    }

    public function assets($hook)
    {
        if ($hook == 'toplevel_page_' . Core::$plugin->prefix . 'main' ||
            $hook == 'post.php' ||
            $hook == $this->pageSettingsHook
        ) {
            wp_enqueue_style(Core::$plugin->prefix . 'adminMain', $this->assetsUrl . '/main.min.css', [], Core::$plugin->version);
            wp_enqueue_script(Core::$plugin->prefix . 'adminMain', $this->assetsUrl . '/main.min.js', ['jquery'], Core::$plugin->version);
            wp_localize_script(Core::$plugin->prefix . 'adminMain', 'lwpwlMain', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce(Core::$plugin->prefix . 'adminMain'),
            ]);
            wp_localize_script(Core::$plugin->prefix . 'adminMain', 'lwpwlFormSubmitControl', [
                'i18nLockLabel' => esc_html__('Sending…', 'luckywp-wiki-linking'),
            ]);
        }
    }
}
