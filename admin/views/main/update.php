<?php
/**
 * @var $item \luckywp\wikiLinking\plugin\entities\Item
 * @var $model \luckywp\wikiLinking\admin\forms\main\UpdateForm
 */

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\admin\widgets\selectPost\SelectPost;
use luckywp\wikiLinking\core\admin\helpers\AdminUrl;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\helpers\Html;
use luckywp\wikiLinking\plugin\entities\ItemType;

?>
<div class="wrap">
    <div class="lwpwlBreadcrumbs">
        <a href="<?= AdminUrl::to('main') ?>"><?= esc_html__('Wiki Linking', 'luckywp-wiki-linking') ?></a>
        <i>/</i>
        <b><?= esc_html__('Edit Keyword Phrase', 'luckywp-wiki-linking') ?></b>
    </div>
    <h1><?= esc_html__('Edit Keyword Phrase', 'luckywp-wiki-linking') ?></h1>

    <?php
    if ($model->hasErrors()) {
        echo '<div class="lwpwlColorError lwpwlMtBase">';
        foreach ($model->getErrorSummary() as $error) {
            echo '<p>' . $error . '</p>';
        }
        echo '</div>';
    }
    ?>

    <form
        action="<?= AdminUrl::to('main', 'update', ['nonce' => wp_create_nonce(Core::$plugin->prefix . 'updateItem'), 'id' => $item->id]) ?>"
        method="post"
        class="lwpwlUpdateItem js-lwpwlFormSubmitControl"
    >
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label><?= $model->getAttributeLabel('anchor') ?> </label>
                    </th>
                    <td>
                        <?= AdminHtml::textInput(
                            Html::getInputName($model, 'anchor'),
                            $model->anchor,
                            [
                                'size' => AdminHtml::TEXT_INPUT_SIZE_LARGE,
                                'class' => 'lwpwlUpdateItem_anchorInput',
                                'data-old' => $model->anchor,
                            ]
                        ) ?>
                        <div class="lwpwlUpdateItem_changeAnchorAlert">
                            <div class="lwpwlUpdateItem_changeAnchorAlert_title">
                                <?= esc_html__('Please note!', 'luckywp-wiki-linking') ?>
                            </div>
                            <?= esc_html__('If you change keyword phrase it will be unbound from all posts.', 'luckywp-wiki-linking') ?>
                            <br>
                            <?= esc_html__("Don't forget to bind a keyword phrase to posts after saving.", 'luckywp-wiki-linking') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?= $model->getAttributeLabel('typeId') ?> </label>
                    </th>
                    <td>
                        <fieldset class="lwpwlUpdateItem_typeRadios">
                            <?php foreach (ItemType::toList() as $value => $label) { ?>
                                <p>
                                    <?= Html::radio(Html::getInputName($model, 'typeId'), $model->typeId == $value, [
                                        'value' => $value,
                                        'label' => $label
                                    ]) ?>
                                </p>
                            <?php } ?>
                        </fieldset>
                    </td>
                </tr>
                <tr class="lwpwlUpdateItem_byType lwpwlUpdateItem_byType-<?= ItemType::CUSTOM ?>"<?= $model->typeId == ItemType::CUSTOM ? '' : ' style="display:none"' ?>>
                    <th>
                        <label><?= $model->getAttributeLabel('url') ?> </label>
                    </th>
                    <td>
                        <?= AdminHtml::textInput(Html::getInputName($model, 'url'), $model->url, ['size' => AdminHtml::TEXT_INPUT_SIZE_LARGE]) ?>
                    </td>
                </tr>
                <tr class="lwpwlUpdateItem_byType lwpwlUpdateItem_byType-<?= ItemType::POST ?>"<?= $model->typeId == ItemType::POST ? '' : ' style="display:none"' ?>>
                    <th>
                        <label><?= $model->getAttributeLabel('postId') ?> </label>
                    </th>
                    <td>
                        <?= SelectPost::widget([
                            'model' => $model,
                            'attribute' => 'postId',
                        ]) ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>
            <?= AdminHtml::button(esc_html__('Save', 'luckywp-wiki-linking'), [
                'submit' => true,
                'theme' => AdminHtml::BUTTON_THEME_PRIMARY,
            ]) ?>
        </p>
    </form>
</div>