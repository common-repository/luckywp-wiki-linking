<?php
/**
 * @var $name string
 * @var $post WP_Post|null
 */

use luckywp\wikiLinking\core\helpers\Html;

?>
<div class="lwpwlSelectPost<?= $post ? '' : ' lwpwlSelectPost-empty' ?>">
    <div class="lwpwlSelectPost_select">
        <?php if ($post) { ?>
            <span class="dashicons dashicons-admin-post"></span>
            <span class="lwpwlSelectPost_select_postTitle">
                <?= $post->post_title ?>
            </span>
        <?php } else { ?>
            <?= esc_html__('Select a post', 'luckywp-wiki-linking') ?>
        <?php } ?>
    </div>
    <div class="lwpwlSelectPost_clear" title="<?= esc_attr__('Clear', 'luckywp-wiki-linking') ?>">
        <span class="dashicons dashicons-no-alt"></span>
    </div>
    
    <?= Html::hiddenInput($name, $post ? $post->ID : null, ['class' => 'lwpwlSelectPost_input']) ?>

    <div class="lwpwlHide">
        <div class="lwpwlSelectPost_modal">
            <div class="lwpwlSelectPost_modal_cancel">
                <div class="lwpwlSelectPost_modal_cancel_do lwpwlModal-close">
                    <span class="dashicons dashicons-no-alt"></span>
                    <?= esc_html__('Cancel', 'luckywp-wiki-linking') ?>
                </div>
            </div>
            <div class="lwpwlSelectPost_modal_input">
                <input type="text" value="" placeholder="<?= esc_attr__('Search for a post…', 'luckywp-wiki-linking') ?>">
            </div>
            <div class="lwpwlSelectPost_modal_process">
                <div class="lwpwlSpinner">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            <div class="lwpwlSelectPost_modal_body"></div>
        </div>
    </div>
</div>