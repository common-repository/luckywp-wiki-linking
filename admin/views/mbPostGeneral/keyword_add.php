<?php
/**
 * @var $post WP_Post
 * @var $model \luckywp\wikiLinking\core\base\Model
 */

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\helpers\Html;

?>
<div class="lwpwlModalBox lwpwlModalPostKeywordAdd">
    <div class="lwpwlModalBox_close lwpwlModal-close" title="<?= esc_attr__('Cancel', 'luckywp-wiki-linking') ?>"></div>
    <div class="lwpwlModalBox_title"><?= esc_html__('New Keyword Phrases', 'luckywp-wiki-linking') ?></div>
    <form
        action="<?= admin_url('admin-ajax.php?_ajax_nonce=' . wp_create_nonce(Core::$plugin->prefix . 'adminMain') . '&action=lwpwlmb_post_general_keyword_add&postId=' . $post->ID) ?>"
        data-ajax-form="1"
    >
        <div class="lwpwlModalBox_body">

            <div class="lwpwlModalForm_field">
                <div class="lwpwlModalPostKeywordAdd_label">
                    <?= esc_html__('Enter new keyword phrases, one per line:', 'luckywp-wiki-linking') ?>
                </div>
                <div class="lwpwlModalForm_field_el">
                    <?= Html::textarea(Html::getInputName($model, 'keywords'), $model->keywords, ['rows' => 6]) ?>
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
                <?= AdminHtml::button(esc_html__('Add', 'luckywp-wiki-linking'), [
                    'theme' => AdminHtml::BUTTON_THEME_PRIMARY,
                    'submit' => true,
                ]) ?>
            </div>
        </div>
    </form>
</div>