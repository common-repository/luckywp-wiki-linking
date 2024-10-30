<?php
/**
 * @var $item \luckywp\wikiLinking\plugin\entities\Item
 */

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\admin\helpers\AdminUrl;
use luckywp\wikiLinking\core\helpers\Html;

?>
<div class="lwpwlItems_card" data-id="<?= $item->id ?>">
    <div class="lwpwlItems_card_hide">
        <?= esc_html__('hide', 'luckywp-wiki-linking') ?>
    </div>
    <div class="lwpwlItems_card_anchor"><?= $item->anchor ?></div>
    <div class="lwpwlItems_card_url">
        <?= Html::a(
            $item->isCustom ? $item->url : $item->post->post_title,
            $item->generateUrl()
        ) ?>
    </div>
    <div class="lwpwlItems_card_buttons">
        <?= AdminHtml::button(esc_html__('Bind to posts', 'luckywp-wiki-linking'), ['class' => 'lwpwlItems_card_bind']) ?>
        <?= AdminHtml::button(esc_html__('Edit', 'luckywp-wiki-linking'), [
            'href' => AdminUrl::to('main', 'update', ['id' => $item->id]),
            'theme' => AdminHtml::BUTTON_THEME_LINK,
        ]) ?>
        <?= AdminHtml::button(esc_html__('Delete', 'luckywp-wiki-linking'), [
            'theme' => AdminHtml::BUTTON_THEME_LINK_DELETE,
            'class' => 'lwpwlItems_deleteItemFromCard',
        ]) ?>
    </div>
    <?php if ($item->linkedPosts) { ?>
        <div class="lwpwlItems_card_linkedPosts">
            <?php foreach ($item->linkedPosts as $post) { ?>
                <div>
                    <a href="<?= get_permalink($post) ?>"><?= $post->post_title ?></a>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="lwpwlItems_card_noLinkedPosts">
            <?= esc_html__("Keyword phrase isn't bound to any post", 'luckywp-wiki-linking') ?>
        </div>
    <?php } ?>
</div>
