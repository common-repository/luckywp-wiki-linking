<?php
/**
 * @var $model \luckywp\wikiLinking\core\base\Model
 */

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\helpers\Html;

?>
<div class="lwpwlModalBox lwpwlModalPostKeywordUpdate">
    <div class="lwpwlModalBox_close lwpwlModal-close" title="<?= esc_attr__('Cancel', 'luckywp-wiki-linking') ?>"></div>
    <div class="lwpwlModalBox_title"><?= esc_html__('Keyword phrase', 'luckywp-wiki-linking') ?></div>
    <form
        action="<?= admin_url('admin-ajax.php?_ajax_nonce=' . wp_create_nonce(Core::$plugin->prefix . 'adminMain') . '&action=lwpwlmb_post_general_keyword_update&id=' . $model->item->id) ?>"
        data-ajax-form="1"
    >
        <div class="lwpwlModalBox_body">

            <div class="lwpwlModalPostKeywordUpdate_tip">
                <b><?= esc_html__('Please note!', 'luckywp-wiki-linking') ?></b>
                <br>
                <?= esc_html__('If you change keyword phrase it will be unbound from all posts.', 'luckywp-wiki-linking') ?>
                <br>
                <?= esc_html__("Don't forget to bind a keyword phrase to posts after saving.", 'luckywp-wiki-linking') ?>
            </div>

            <div class="lwpwlModalForm_field">
                <div class="lwpwlModalForm_field_el">
                    <?= Html::textInput(Html::getInputName($model, 'anchor'), $model->anchor) ?>
                </div>
            </div>

            <?php
            if ($model->hasErrors()) {
                echo '<div class="lwpwlModalForm_errors lwpwlMtHalf">';
                foreach ($model->getErrorSummary() as $error) {
                    echo '<p>' . $error . '</p>';
                }
                echo '</div>';
            }
            ?>

        </div>
        <div class="lwpwlModalBox_footer">
            <div class="lwpwlModalBox_footer_buttons">
                <?= AdminHtml::button(esc_html__('Cancel', 'luckywp-wiki-linking'), [
                    'class' => 'lwpwlModal-close lwpwlFloatLeft'
                ]) ?>
                <?= AdminHtml::button(esc_html__('Save', 'luckywp-wiki-linking'), [
                    'theme' => AdminHtml::BUTTON_THEME_PRIMARY,
                    'submit' => true,
                ]) ?>
            </div>
        </div>
    </form>
</div>