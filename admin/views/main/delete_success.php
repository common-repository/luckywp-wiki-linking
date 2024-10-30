<?php
/**
 * @var $item \luckywp\wikiLinking\plugin\entities\Item
 */
?>
<div class="lwpwlModalSuccess lwpwlModal-close">
    <div class="lwpwlModalSuccess_ico"></div>
    <div class="lwpwlModalSuccess_text">
        <?= esc_html__('Keyword phrase removed', 'luckywp-wiki-linking') ?>
    </div>
    <div class="lwpwlModalSuccess_close">
        <?= esc_html__('Close', 'luckywp-wiki-linking') ?>
    </div>
</div>
<script>jQuery(document).trigger('lwpwlItemDeleted', {id: <?= $item->id ?>});</script>