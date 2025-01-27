<?php

namespace luckywp\wikiLinking\core\wp;

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\core\base\BaseObject;
use luckywp\wikiLinking\core\base\Widget;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\core\helpers\ArrayHelper;
use luckywp\wikiLinking\core\helpers\Html;

class Settings extends BaseObject
{

    /**
     * Префикс. Добавляется к названию секций и групп
     * @var string
     */
    protected $prefix;

    /**
     * Массив описывает группы опций.
     * Каждый элемент массива — массив с параметрами группы.
     * Парамтеры:
     *    id - идентификатор
     *    name - идентификатор без префикса
     *    label - заголовок (используется, как заголовок таба)
     * @var array
     */
    protected $groups = [];

    /**
     * Массив описывает секции ($groupId => $config)
     * Каждый элемент массива — массив с параметрами секции
     * Параметры секций:
     *    id - идентификатор (должно быть уникально)
     *    title - заголовок
     *    desc - описание
     *    group - ID группы
     * @var array
     */
    protected $sections = [];


    /**
     * Массив описывает поля ($groupId => $config)
     * Каждый элемент массива — массив с параметрами поля
     * Параметры полей:
     *    id - идентификатор (должно быть уникально в рамках группы)
     *    label - заголовок
     *    desc - описание
     *    default - значение по-умолчанию
     *    widget - виджет для ввода
     *    params - параметры виджета
     *    sanitizeCallback - функция для валидации и подготовки значения
     *    section - ID секции
     *    group - ID группы
     *    name - атрибут name для виджета
     * @var array
     */
    protected $fields = [];

    /**
     * Конфиг для инициализации
     * @var array
     */
    public $initGroupsConfig = [];

    /**
     * Файл с конфигом для инициализации
     * @var array
     */
    public $initGroupsConfigFile;

    public function init()
    {
        if ($this->prefix === null) {
            $this->prefix = Core::$plugin->prefix;
        }

        $config = [];
        if ($this->initGroupsConfig) {
            $config = $this->initGroupsConfig;
        } elseif ($this->initGroupsConfigFile !== null) {
            $config = require($this->initGroupsConfigFile);
        }
        if ($config) {
            $this->addGroups($config);
        }

        if (is_admin() && !wp_doing_ajax()) {
            add_action('admin_init', [$this, 'adminInit']);
        }
    }

    public function adminInit()
    {
        // Добавляем секции
        foreach ($this->sections as $groupId => $sections) {
            foreach ($sections as $sectionId => $section) {
                add_settings_section(
                    $section['id'],
                    $section['title'],
                    [$this, 'sectionCallback'],
                    $section['group']
                );
            }
        }

        // Добавляем поля
        foreach ($this->fields as $groupId => $fields) {
            foreach ($fields as $fieldId => $field) {
                $callback = null;
                if (is_callable($field['widget'])) {
                    $callback = $field['widget'];
                } elseif (is_string($field['widget'])) {
                    $methodName = 'field' . ucfirst($field['widget']);
                    if (method_exists($this, $methodName)) {
                        $callback = [$this, $methodName];
                    }
                }
                add_settings_field(
                    $field['id'],
                    $field['label'],
                    $callback,
                    $field['group'],
                    $field['section'],
                    $field
                );
            }
        }

        // Регистрация настроек
        foreach ($this->groups as $group) {
            register_setting(
                $group['id'],
                $group['id'],
                [$this, 'sanitizeCallback']
            );
        }
    }

    /**
     * Добавление групп
     * @param array $config
     * @return $this
     */
    public function addGroups($config)
    {
        foreach ($config as $groupId => $groupConfig) {
            $this->addGroup($groupId, $groupConfig);
        }
        return $this;
    }

    /**
     * Добавление группы
     * @param string $groupId
     * @param array $config
     * @return $this
     */
    public function addGroup($groupId, $config)
    {
        $group = [
            'id' => $this->prefix . $groupId,
            'name' => $groupId,
            'label' => ArrayHelper::getValue($config, 'label', $groupId),
        ];
        $this->groups[$group['id']] = $group;
        $this->addSections($groupId, ArrayHelper::getValue($config, 'sections', []));
        return $this;
    }

    /**
     * Добавление секций
     * @param string $groupId
     * @param array $config
     * @return $this
     */
    public function addSections($groupId, $config)
    {
        foreach ($config as $sectionId => $sectionConfig) {
            $this->addSection($sectionId, $groupId, $sectionConfig);
        }
        return $this;
    }

    /**
     * Добавление секции
     * @param string $sectionId
     * @param string $groupId
     * @param array $config
     * @return $this
     */
    public function addSection($sectionId, $groupId, $config)
    {
        $title = ArrayHelper::getValue($config, 'title');
        if (is_callable($title)) {
            $title = call_user_func($title);
        }
        $section = [
            'id' => $this->prefix . $sectionId,
            'group' => $this->prefix . $groupId,
            'title' => $title,
            'desc' => ArrayHelper::getValue($config, 'desc'),
        ];
        $this->sections[$section['group']][$section['id']] = $section;
        $this->addFields($groupId, $sectionId, ArrayHelper::getValue($config, 'fields', []));
        return $this;
    }

    /**
     * Добавление полей
     * @param string $groupId
     * @param string $sectionId
     * @param array $config
     * @return $this
     */
    public function addFields($groupId, $sectionId, $config)
    {
        foreach ($config as $fieldId => $fieldConfig) {
            $this->addField($fieldId, $groupId, $sectionId, $fieldConfig);
        }
        return $this;
    }

    /**
     * Добавление поля
     * @param string $fieldId
     * @param string $groupId
     * @param string $sectionId
     * @param array $config
     * @return $this
     */
    public function addField($fieldId, $groupId, $sectionId, $config)
    {
        $field = [
            'id' => $fieldId,
            'group' => $this->prefix . $groupId,
            'section' => $this->prefix . $sectionId,
            'label' => ArrayHelper::getValue($config, 'label'),
            'desc' => ArrayHelper::getValue($config, 'desc'),
            'default' => ArrayHelper::getValue($config, 'default'),
            'widget' => ArrayHelper::getValue($config, 'widget', 'textInput'),
            'params' => ArrayHelper::getValue($config, 'params', []),
            'sanitizeCallback' => ArrayHelper::getValue($config, 'sanitizeCallback'),
            'name' => $this->prefix . $groupId . '[' . $fieldId . ']',
        ];
        $this->fields[$field['group']][$field['id']] = $field;
        return $this;
    }

    public function install()
    {
        $options = [];
        foreach ($this->fields as $groupId => $fields) {
            foreach ($fields as $fieldId => $field) {
                if (!is_null($field['default'])) {
                    if (!isset($options[$field['group']])) {
                        $options[$field['group']] = [];
                    }
                    $options[$field['group']][$field['id']] = $field['default'];
                }
            }
        }
        foreach ($options as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
    }

    /**
     * Вывод описания секции
     * @param $arg
     */
    public function sectionCallback($arg)
    {
        foreach ($this->sections as $sections) {
            if (array_key_exists($arg['id'], $sections)) {
                echo $sections[$arg['id']]['desc'];
            }
        }
    }

    /**
     * Подготовка и валидация значений
     * @param $options
     * @return mixed
     */
    public function sanitizeCallback($options)
    {
        if (!is_array($options)) {
            return [];
        }
        $currentGroup = $this->getCurrentGroup();
        if ($currentGroup === false) {
            return [];
        }
        foreach ($options as $id => $value) {
            $oldValue = $this->getValue($currentGroup['id'], $id, null, false);

            if (is_string($this->fields[$currentGroup['id']][$id]['widget'])) {
                $methodName = 'sanitizeCallback' . ucfirst($this->fields[$currentGroup['id']][$id]['widget']);
                if (method_exists($this, $methodName)) {
                    $options[$id] = call_user_func([$this, $methodName], $value, $oldValue, $id);
                }
            }

            $callable = ArrayHelper::getValue($this->fields[$currentGroup['id']][$id], 'sanitizeCallback');
            if (is_callable($callable)) {
                $options[$id] = call_user_func($callable, $value, $oldValue, $id);
            }
        }
        return $options;
    }

    /**
     * Возвращает значение опции
     * @param $groupId
     * @param $fieldId
     * @param string $default
     * @param bool $prepareArgs
     * @return string
     */
    public function getValue($groupId, $fieldId, $default = null, $prepareArgs = true)
    {
        if ($prepareArgs) {
            $groupId = $this->prefix . $groupId;
        }
        $options = get_option($groupId);
        if (is_array($options) && array_key_exists($fieldId, $options)) {
            return $options[$fieldId];
        }
        return $default;
    }

    /**
     * Возвращает все значения опций группы
     * @param string $groupId
     * @return array
     */
    public function getValues($groupId)
    {
        $values = get_option($this->prefix . $groupId);
        return is_array($values) ? $values : [];
    }


    /**
     * ---------------------------------------------------------------------------
     *  Вывод и обработка формы
     * ---------------------------------------------------------------------------
     */

    /**
     * Возвращает текущую группу
     * @return array|false
     */
    private function getCurrentGroup()
    {
        if (!count($this->groups)) {
            return false;
        }

        $tab = ArrayHelper::getValue($_GET, 'tab');
        if (($tab != '') && (isset($this->groups[$this->prefix . $tab]))) {
            return $this->groups[$this->prefix . $tab];
        }

        reset($this->groups);
        $group = current($this->groups);
        return $group;
    }

    /**
     * Вывод табов
     */
    public function showTabs()
    {
        // Активная группа
        $currentGroup = $this->getCurrentGroup();
        if ($currentGroup === false) {
            return;
        }

        // Вывод
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($this->groups as $group) {
            echo '<a href="?page=' . $_GET['page'] . '&tab=' . $group['name'] . '" class="nav-tab' . ($group['name'] == $currentGroup['name'] ? ' nav-tab-active' : '') . '">' . $group['label'] . '</a>';
        }
        echo '</h2>';
    }

    /**
     * Вывод формы
     * @param string|null $groupId
     */
    public function showForm($groupId = null)
    {
        if ($groupId === null) {
            $currentGroup = $this->getCurrentGroup();
            if ($currentGroup === false) {
                return;
            }
            $groupId = $currentGroup['id'];
        } else {
            $groupId = $this->prefix . $groupId;
        }

        echo '<form action="options.php" method="POST">';
        settings_fields($groupId);
        do_settings_sections($groupId);
        submit_button();
        echo '</form>';
    }

    /**
     * Вывод страницы с настройками
     * @param bool $showErrors
     */
    public function showPage($showErrors = true)
    {
        if ($showErrors) {
            settings_errors();
        }
        if (count($this->groups) > 1) {
            $this->showTabs();
        }
        $this->showForm();
    }


    /**
     * ---------------------------------------------------------------------------
     *  Поля
     * ---------------------------------------------------------------------------
     */

    /**
     * @param array $field
     */
    public function fieldTextInput($field)
    {
        $value = $this->getValue($field['group'], $field['id'], '', false);

        // Параметры
        $params = $field['params'];
        $inputOptions = isset($params['inputOptions']) ? $params['inputOptions'] : [];

        // Вывод
        echo AdminHtml::textInput(
            $field['name'],
            $value,
            $inputOptions
        );
        if ($field['desc'] != '') {
            echo '<p class="description">' . $field['desc'] . '</p>';
        }
    }

    /**
     * @param array $field
     */
    public function fieldWidget($field)
    {
        $params = $field['params'];
        $params['name'] = $field['name'];
        $params['value'] = $this->getValue($field['group'], $field['id'], '', false);
        $params['desc'] = ArrayHelper::getValue($field, 'desc');

        /** @var Widget $widget */
        $widget = Core::createObject($params);
        echo $widget->run();
    }

    /**
     * @param array $field
     */
    public function fieldCheckbox($field)
    {
        $checked = (bool)$this->getValue($field['group'], $field['id'], false, false);

        // Параметры
        $params = $field['params'];
        $checkboxOptions = isset($params['checkboxOptions']) ? $params['checkboxOptions'] : [];
        $checkboxOptions['value'] = 1;
        $checkboxOptions['uncheck'] = 0;

        // Вывод
        echo Html::checkbox($field['name'], $checked, $checkboxOptions);
        if ($field['desc'] != '') {
            echo '<p class="description">' . $field['desc'] . '</p>';
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function sanitizeCallbackCheckbox($value)
    {
        return (bool)$value;
    }
}
