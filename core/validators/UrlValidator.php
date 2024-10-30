<?php

namespace luckywp\wikiLinking\core\validators;

class UrlValidator extends Validator
{
    /**
     * @var string
     */
    public $pattern = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i';

    /**
     * @var array
     */
    public $validSchemes = ['http', 'https'];

    /**
     * @var string
     */
    public $defaultScheme;


    public function init()
    {
        if ($this->message === null) {
            $this->message = esc_html__('{attribute} is not a valid URL.', 'luckywp-wiki-linking');
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $result = $this->validateValue($value);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        } elseif ($this->defaultScheme !== null && strpos($value, '://') === false) {
            $model->$attribute = $this->defaultScheme . '://' . $value;
        }
    }

    protected function validateValue($value)
    {
        // make sure the length is limited to avoid DOS attacks
        if (is_string($value) && strlen($value) < 2000) {
            if ($this->defaultScheme !== null && strpos($value, '://') === false) {
                $value = $this->defaultScheme . '://' . $value;
            }

            if (strpos($this->pattern, '{schemes}') !== false) {
                $pattern = str_replace('{schemes}', '(' . implode('|', $this->validSchemes) . ')', $this->pattern);
            } else {
                $pattern = $this->pattern;
            }

            if (preg_match($pattern, $value)) {
                return null;
            }
        }

        return [$this->message, []];
    }
}
