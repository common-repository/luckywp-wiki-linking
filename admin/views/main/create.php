<?php
/**
 * @var $model \luckywp\wikiLinking\admin\forms\main\CreateForm
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
        <b><?= esc_html__('New Keyword Phrase', 'luckywp-wiki-linking') ?></b>
    </div>
    <h1><?= esc_html__('New Keyword Phrase', 'luckywp-wiki-linking') ?></h1>

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
        action="<?= AdminUrl::to('main', 'create', ['nonce' => wp_create_nonce(Core::$plugin->prefix . 'createItem')]) ?>"
        method="post"
        class="lwpwlCreateItem js-lwpwlFormSubmitControl"
    >
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label><?= $model->getAttributeLabel('anchor') ?> </label>
                    </th>
                    <td>
                        <?= AdminHtml::textInput(Html::getInputName($model, 'anchor'), $model->anchor, ['size' => AdminHtml::TEXT_INPUT_SIZE_LARGE]) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?= $model->getAttributeLabel('typeId') ?> </label>
                    </th>
                    <td>
                        <fieldset class="lwpwlCreateItem_typeRadios">
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
                <tr class="lwpwlCreateItem_byType lwpwlCreateItem_byType-<?= ItemType::CUSTOM ?>"<?= $model->typeId == ItemType::CUSTOM ? '' : ' style="display:none"' ?>>
                    <th>
                        <label><?= $model->getAttributeLabel('url') ?> </label>
                    </th>
                    <td>
                        <?= AdminHtml::textInput(Html::getInputName($model, 'url'), $model->url, ['size' => AdminHtml::TEXT_INPUT_SIZE_LARGE]) ?>
                    </td>
                </tr>
                <tr class="lwpwlCreateItem_byType lwpwlCreateItem_byType-<?= ItemType::POST ?>"<?= $model->typeId == ItemType::POST ? '' : ' style="display:none"' ?>>
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
            <?= AdminHtml::button(esc_html__('Add', 'luckywp-wiki-linking'), [
                'submit' => true,
                'theme' => AdminHtml::BUTTON_THEME_PRIMARY,
            ]) ?>
        </p>
    </form>
</div>