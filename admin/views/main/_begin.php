<?php

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\admin\helpers\AdminUrl;
use luckywp\wikiLinking\core\Core;

?>
<div class="lwpwlItems_empty">
    <div class="lwpwlItems_empty_header">
        <span class="dashicons dashicons-flag"></span>
        <?= esc_html__("Let's start!", 'luckywp-wiki-linking') ?>
    </div>
    <div class="lwpwlItems_empty_body">
        <p>
            <?php
            printf(
                esc_html__('The %s plugin allows to realize internal linking on the website.', 'luckywp-wiki-linking'),
                '<b>LuckyWP Wiki Linking</b>'
            );
            ?>
        </p>
        <p>
            <?= esc_html__('You can add keyword phrases and the link address or a post to which the link has to be directed and the plugin places them in texts of posts.', 'luckywp-wiki-linking') ?>
        </p>
        <p>
            <?= esc_html__('Please note that the link is displayed ONLY on the single page of a post, in all other places (RSS feed, the editor on the control panel and etc.) this link will NOT be displayed.', 'luckywp-wiki-linking') ?>
        </p>

        <div class="lwpwlItems_empty_body_sep"></div>

        <p>
            <?= esc_html__('When placing a link in a post, whole words are only taken into account.', 'luckywp-wiki-linking') ?>
            <br>
            <?php
            printf(
                esc_html__('For example, the link with the keyword phrase %s will be added to the text:', 'luckywp-wiki-linking'),
                '<b>bus</b>'
            );
            ?>
        </p>
        <div class="lwpwlItems_empty_body_quote">
            She got on the <a href="#">bus</a> at Clark Street.
        </div>
        <p>
            <?= esc_html__('and the link will NOT be added to the text:', 'luckywp-wiki-linking') ?>
        </p>
        <div class="lwpwlItems_empty_body_quote">
            I'm here on business.
        </div>

        <div class="lwpwlItems_empty_body_sep"></div>

        <p>
            <?= esc_html__('Maximum number of links placed in one post (the settings can be changed):', 'luckywp-wiki-linking') ?>
        </p>
        <ul>
            <?php
            foreach (Core::$plugin->getPostTypes() as $postType) {
                echo '<li>' . $postType->label . ': <b>' . Core::$plugin->getItemsPerPost($postType->name) . '</b></li>';
            }
            ?>
        </ul>

        <div class="lwpwlItems_empty_body_sep"></div>

        <p>
            <?php
            printf(
                esc_html__('Maximum number of links for a keyword phrase: %s (the settings can be changed).', 'luckywp-wiki-linking'),
                '<b>' . Core::$plugin->settings->getValue('general', 'posts_per_item') . '</b>'
            );
            ?>
        </p>
    </div>
    <div class="lwpwlItems_empty_actions">
        <?= AdminHtml::button(esc_html__('Add the first keyword phrase', 'luckywp-wiki-linking'), [
            'href' => AdminUrl::to('main', 'create'),
            'theme' => AdminHtml::BUTTON_THEME_PRIMARY,
            'size' => AdminHtml::BUTTON_SIZE_LARGE,
        ]) ?>
        <?= AdminHtml::button(esc_html__('Change the settings', 'luckywp-wiki-linking'), [
            'href' => AdminUrl::to('settings'),
            'theme' => AdminHtml::BUTTON_THEME_SECONDARY,
            'size' => AdminHtml::BUTTON_SIZE_LARGE,
        ]) ?>
    </div>
</div>