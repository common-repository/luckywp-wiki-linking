<?php
/**
 * @var $item \luckywp\wikiLinking\plugin\entities\Item
 */

use luckywp\wikiLinking\admin\helpers\AdminHtml;

?>
<div class="lwpwlModalBox lwpwlModalPostKeyword">
    <div class="lwpwlModalBox_close lwpwlModal-close" title="<?= esc_attr__('Close', 'luckywp-wiki-linking') ?>"></div>
    <div class="lwpwlModalBox_title"><?= esc_html__('Keyword phrase', 'luckywp-wiki-linking') ?></div>
    <div class="lwpwlModalBox_body">
        <div class="lwpwlModalPostKeyword_anchor">
            <?= $item->anchor ?>
        </div>
        <?php if ($item->linkedPosts) { ?>
            <div class="lwpwlModalPostKeyword_linkedPosts">
                <?php foreach ($item->linkedPosts as $post) { ?>
                    <div>
                        <a href="<?= get_permalink($post) ?>"><?= $post->post_title ?></a>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="lwpwlModalPostKeyword_noLinkedPosts">
                <?= esc_html__("Keyword phrase isn't bound to any post", 'luckywp-wiki-linking') ?>
            </div>
        <?php } ?>
    </div>
    <div class="lwpwlModalBox_footer">
        <div class="lwpwlModalBox_footer_buttons">
            <?= AdminHtml::button(esc_html__('Close', 'luckywp-wiki-linking'), [
                'class' => 'lwpwlModal-close'
            ]) ?>
        </div>
    </div>
</div>