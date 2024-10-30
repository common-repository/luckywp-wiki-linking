<?php

namespace luckywp\wikiLinking\core\validators;

class RequiredValidator extends Validator
{
    /**
     * @var bool
     */
    public $skipOnEmpty = false;


    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = esc_html__('{attribute} cannot be blank.', 'luckywp-wiki-linking');
        }
    }

    /**
     * @param mixed $value
     * @return array|null
     */
    protected function validateValue($value)
    {
        if (!$this->isEmpty(is_string($value) ? trim($value) : $value)) {
            return null;
        }
        return [$this->message, []];
    }
}
