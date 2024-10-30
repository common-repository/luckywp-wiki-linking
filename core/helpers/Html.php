<?php

namespace luckywp\wikiLinking\core\helpers;

use luckywp\wikiLinking\core\base\Model;

class Html
{

    /**
     * @var array
     * @see https://www.w3.org/TR/html/syntax.html#void-elements
     */
    public static $voidElements = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    /**
     * @param string|false|null $name
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function tag($name, $content = '', $options = [])
    {
        if ($name === null || $name === false) {
            return $content;
        }
        $html = "<$name" . static::renderTagAttributes($options) . '>';
        return array_key_exists(strtolower($name), static::$voidElements) ? $html : "$html$content</$name>";
    }

    /**
     * @param string|false|null $name
     * @param array $options
     * @return string
     */
    public static function beginTag($name, $options = [])
    {
        if ($name === null || $name === false) {
            return '';
        }
        return "<$name" . static::renderTagAttributes($options) . '>';
    }

    public static function renderTagAttributes($attributes)
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if ($name == 'data') {
                    foreach ($value as $n => $v) {
                        if (is_array($v)) {
                            $html .= " $name-$n='" . Json::htmlEncode($v) . "'";
                        } else {
                            $html .= " $name-$n=\"" . static::encode($v) . '"';
                        }
                    }
                } elseif ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(implode(' ', $value)) . '"';
                } elseif ($name === 'style') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(static::cssStyleFromArray($value)) . '"';
                } else {
                    $html .= " $name='" . Json::htmlEncode($value) . "'";
                }
            } elseif ($value !== null) {
                $html .= " $name=\"" . static::encode($value) . '"';
            }
        }

        return $html;
    }

    public static function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    public static function cssStyleFromArray(array $style)
    {
        $result = '';
        foreach ($style as $name => $value) {
            $result .= "$name: $value; ";
        }
        return $result === '' ? null : rtrim($result);
    }

    public static function prepareClassInOptions(array $options)
    {
        if (!isset($options['class'])) {
            $options['class'] = [];
        }
        if (!is_array($options['class'])) {
            $options['class'] = [$options['class']];
        }
        return $options;
    }

    public static function label($content, $for = null, $options = [])
    {
        $options['for'] = $for;
        return static::tag('label', $content, $options);
    }

    public static function input($type, $name = null, $value = null, $options = [])
    {
        if (!isset($options['type'])) {
            $options['type'] = $type;
        }
        $options['name'] = $name;
        $options['value'] = $value === null ? null : (string)$value;
        return static::tag('input', '', $options);
    }

    public static function textInput($name, $value = null, $options = [])
    {
        return static::input('text', $name, $value, $options);
    }

    public static function textarea($name, $value = '', $options = [])
    {
        $options['name'] = $name;
        $doubleEncode = ArrayHelper::remove($options, 'doubleEncode', true);
        return static::tag('textarea', static::encode($value, $doubleEncode), $options);
    }

    public static function a($text, $url = null, $options = [])
    {
        if ($url !== null) {
            $options['href'] = $url;
        }
        return static::tag('a', $text, $options);
    }

    /**
     * @param string $type "radio" или "checkbox"
     * @param string $name
     * @param bool $checked
     * @param array $options
     * @return string
     */
    protected static function booleanInput($type, $name, $checked = false, $options = [])
    {
        $options['checked'] = (bool)$checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';

        $hidden = '';
        if (isset($options['uncheck'])) {
            $hiddenOptions = [];
            $hidden = static::hiddenInput($name, $options['uncheck'], $hiddenOptions);
            unset($options['uncheck']);
        }

        if (isset($options['label'])) {
            $label = $options['label'];
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
            unset($options['label'], $options['labelOptions']);
            $content = static::label(static::input($type, $name, $value, $options) . ' ' . $label, null, $labelOptions);
            return $hidden . $content;
        }
        return $hidden . static::input($type, $name, $value, $options);
    }

    /**
     * @param string $name
     * @param bool $checked
     * @param array $options
     * @return string
     */
    public static function radio($name, $checked = false, $options = [])
    {
        return static::booleanInput('radio', $name, $checked, $options);
    }

    /**
     * @param string $name
     * @param bool $checked
     * @param array $options
     * @return string
     */
    public static function checkbox($name, $checked = false, $options = [])
    {
        return static::booleanInput('checkbox', $name, $checked, $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function hiddenInput($name, $value = null, $options = [])
    {
        return static::input('hidden', $name, $value, $options);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @return string
     */
    public static function getInputName($model, $attribute)
    {
        $formName = $model->formName();
        return $formName . '[' . $attribute . ']';
    }
}
