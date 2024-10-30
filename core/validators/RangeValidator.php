<?php

namespace luckywp\wikiLinking\core\validators;

class RangeValidator extends Validator
{

    /**
     * @var array
     */
    public $range;

    /**
     * @var bool
     */
    public $strict = false;

    /**
     * @var bool
     */
    public $not = false;


    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = esc_html__('{attribute} is invalid.', 'luckywp-wiki-linking');
        }
    }

    protected function validateValue($value)
    {
        $in = in_array($value, $this->range, $this->strict);
        return $this->not !== $in ? null : [$this->message, []];
    }
}
