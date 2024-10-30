<?php

namespace luckywp\wikiLinking\core\helpers;

class ArrayHelper
{

    /**
     * @param array|object $array
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function getValue($array, $key, $default = null)
    {
        if (is_object($array)) {
            return $array->$key;
        }
        if (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        }
        return $default;
    }

    /**
     * @param array $array
     * @param string $from
     * @param string $to
     * @param string $group
     * @return array
     */
    public static function map($array, $from, $to, $group = null)
    {
        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            $value = static::getValue($element, $to);
            if ($group !== null) {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @param array $array
     * @param string $name
     * @param bool $keepKeys
     * @return array
     */
    public static function getColumn($array, $name, $keepKeys = true)
    {
        $result = [];
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = static::getValue($element, $name);
            }
        }
        return $result;
    }

    public static function remove(&$array, $key, $default = null)
    {
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            $value = $array[$key];
            unset($array[$key]);
            return $value;
        }
        return $default;
    }
}
