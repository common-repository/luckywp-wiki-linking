<?php

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\admin\helpers\AdminUrl;

?>
<div class="notice notice-error">
    <p>
        <?php
        printf(
            esc_html__('The functionality of the %s plugin is limited. Links are not displayed!', 'luckywp-wiki-linking'),
            '<b>LuckyWP Wiki Linking</b>'
        );
        ?>
    </p>
    <p><?= esc_html__('Need to update the database.', 'luckywp-wiki-linking') ?></p>
    <p>
        <?= AdminHtml::button(esc_html__('Update', 'luckywp-wiki-linking'), ['href' => AdminUrl::to('dbMigrate')]) ?>
    </p>
</div>