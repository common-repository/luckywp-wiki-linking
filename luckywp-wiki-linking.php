<?php
/*
Plugin Name: LuckyWP Wiki Linking
Plugin URI: https://theluckywp.com/product/wiki-linking/
Description: The plugin allows to easily organize a linking in the wiki style on the website.
Version: 1.0.3
Author: LuckyWP
Author URI: https://theluckywp.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: luckywp-wiki-linking
Domain Path: /languages

LuckyWP Wiki Linking is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

LuckyWP Wiki Linking is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with LuckyWP Wiki Linking. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if (class_exists('lwpwlPremiumAutoloader')) {
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error">
            <p>
                <?php
                printf(
                    esc_html__('%1$s and %2$s plugins cannot work at the same time. Deactivate one of them.', 'luckywp-wiki-linking'),
                    '<b>LuckyWP Wiki Linking</b>',
                    '<b>LuckyWP Wiki Linking Premium</b>'
                );
                ?>
            </p>
        </div>
        <?php
    });
    return;
}

require 'lwpwlAutoloader.php';
$lwpwlAutoloader = new lwpwlAutoloader();
$lwpwlAutoloader->register();
$lwpwlAutoloader->addNamespace('luckywp\wikiLinking', __DIR__);

$config = require(__DIR__ . '/config/plugin.php');
(new \luckywp\wikiLinking\plugin\Plugin($config))->run('1.0.3', __FILE__, 'lwpwl_');
