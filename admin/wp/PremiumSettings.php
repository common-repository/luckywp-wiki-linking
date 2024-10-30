<?php

namespace luckywp\wikiLinking\admin\wp;

use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\helpers\ArrayHelper;
use luckywp\wikiLinking\core\helpers\Html;

class PremiumSettings extends BaseObject
{

    /**
     * @param $field
     */
    public static function checkbox($field)
    {

        // Параметры
        $params = $field['params'];
        $checkboxOptions = isset($params['checkboxOptions']) ? $params['checkboxOptions'] : [];
        $checkboxOptions['disabled'] = true;

        $label = ArrayHelper::getValue($checkboxOptions, 'label', '');
        $label = '<span class="lwpwlColorMuted">' . $label . '</span>';
        $checkboxOptions['label'] = $label;

        // Вывод
        echo Html::checkbox('', false, $checkboxOptions);
        if ($field['desc'] != '') {
            echo '<p class="description">' . $field['desc'] . '</p>';
        }
    }
}
