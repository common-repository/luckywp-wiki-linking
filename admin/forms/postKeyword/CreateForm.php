<?php

namespace luckywp\wikiLinking\admin\forms\postKeyword;

use luckywp\wikiLinking\core\base\Model;
use luckywp\wikiLinking\core\Core;
use luckywp\wikiLinking\plugin\repositories\ItemFilter;

class CreateForm extends Model
{

    public $keywords;

    public function rules()
    {
        return [
            ['keywords', 'required'],
            [
                'keywords',
                function () {
                    $keywords = $this->stringsToArray($this->keywords);

                    $exists = [];
                    $filter = new ItemFilter();
                    foreach ($keywords as $keyword) {
                        $filter->anchor = $keyword;
                        if (Core::$plugin->items->exists($filter)) {
                            $exists[] = $keyword;
                        }
                    }
                    if ($exists) {
                        $this->addError('anchor', esc_html__('Keyword phrases already exist', 'luckywp-wiki-linking') . ':<br>' . implode('<br>', $exists));
                    }

                    if (Core::$plugin->items->count() >= 30) {
                        $keywords = [];
                    } else {
                        $keywords = array_slice($keywords, 0, 30 - Core::$plugin->items->count());
                    }

                    $this->keywords = implode(PHP_EOL, $keywords);
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'keywords' => __('Keyword phrases', 'luckywp-wiki-linking'),
        ];
    }

    public function getKeywordsArray()
    {
        return $this->keywords ? explode(PHP_EOL, $this->keywords) : [];
    }

    /**
     * Преобразовывает многострочный текст в массив строк
     * @param string $string
     * @return array
     */
    private static function stringsToArray($string)
    {
        $keywords = explode(PHP_EOL, $string);
        $keywords = array_map('trim', $keywords);

        // Уникальные строки без учёта регистра, сохранив исходный регистр
        $keywords = array_intersect_key(
            $keywords,
            array_unique(array_map(function ($s) {
                return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
            }, $keywords))
        );

        $keywords = array_filter($keywords, function ($el) {
            return $el != '';
        });
        return $keywords;
    }
}
