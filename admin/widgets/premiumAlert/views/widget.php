<?php
/**
 * @var $text string
 * @var $containerOptions array
 */

use luckywp\wikiLinking\core\helpers\Html;

echo Html::beginTag('div', $containerOptions);
?>
    <div class="lwpwlPremiumAlert_ico"></div>
    <div class="lwpwlPremiumAlert_text">
        <?= $text ?>
    </div>
    <div class="lwpwlPremiumAlert_close lwpwlModal-close">
        <?= esc_html__('Close', 'luckywp-wiki-linking') ?>
    </div>
<?php
echo '</div>';
