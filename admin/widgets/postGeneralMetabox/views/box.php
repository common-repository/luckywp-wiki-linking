<?php
/**
 * @var $post WP_Post
 * @var $items \luckywp\wikiLinking\plugin\entities\Item[]
 */

use luckywp\wikiLinking\admin\helpers\AdminHtml;

?>
<div class="lwpwlmbPostGeneral_keywords">
    <div class="lwpwlmbPostGeneral_keywords_header">
        <?= esc_html__('In contents of other posts on the website the links to the current post with the specified keyword phrase will be displayed as the text of the link.', 'luckywp-wiki-linking') ?>
    </div>
    <?php if ($items) { ?>
        <div class="lwpwlmbPostGeneral_keywords_body">
            <table class="lwpwlGridView">
                <thead>
                    <tr>
                        <th>
                            <?= esc_html__('Keyword phrase', 'luckywp-wiki-linking') ?>
                        </th>
                        <th class="lwpwlTextCenter">
                            <?= esc_html__('Posts', 'luckywp-wiki-linking') ?>
                            <span class="dashicons dashicons-editor-help" title="<?= esc_attr__('Number of posts with displayed link', 'luckywp-wiki-linking') ?>"></span>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) { ?>
                        <tr class="lwpwlmbPostGeneral_keyword" data-id="<?= $item->id ?>">
                            <td class="lwpwlmbPostGeneral_keyword_view">
                                <?= $item->anchor ?>
                            </td>
                            <td class="lwpwlmbPostGeneral_keyword_view lwpwlmbPostGeneral_keyword_countPosts lwpwlTextCenter">
                                <?= count($item->linkedPosts) ?>
                            </td>
                            <td>
                            <span
                                class="dashicons dashicons-update lwpwlGridView_actionIco lwpwlmbPostGeneral_keyword_bind"
                                title="<?= esc_attr__('Bind posts', 'luckywp-wiki-linking') ?>"
                            ></span>
                                <span
                                    class="dashicons dashicons-edit lwpwlGridView_actionIco lwpwlmbPostGeneral_keyword_update"
                                    title="<?= esc_attr__('Edit', 'luckywp-wiki-linking') ?>"
                                ></span>
                                <span
                                    class="dashicons dashicons-no lwpwlGridView_actionIco lwpwlGridView_actionIco-danger lwpwlmbPostGeneral_keyword_delete"
                                    title="<?= esc_attr__('Delete', 'luckywp-wiki-linking') ?>"
                                ></span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?= AdminHtml::button(esc_html__('Add', 'luckywp-wiki-linking'), ['class' => 'lwpwlmbPostGeneral_keywords_add']) ?>
    <?php } else { ?>
        <div class="lwpwlmbPostGeneral_keywords_empty">
            <?= esc_html__('There are no keyword phrases', 'luckywp-wiki-linking') ?>
            <div class="lwpwlmbPostGeneral_keywords_empty_btn">
                <?= AdminHtml::button(esc_html__('Add', 'luckywp-wiki-linking'), ['class' => 'lwpwlmbPostGeneral_keywords_add']) ?>
            </div>
        </div>
    <?php } ?>
</div>