<?php
namespace Carbon_Fields\Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Hidden_Field extends Field
{
    /**
     * Underscore template of this field.
     */
    public function template() {
        ?>
        <input id="{{{ id }}}" type="hidden" name="{{{ name }}}" value="{{ value }}"/>
        <?php
    }

    /**
     * @inheritdoc
     * @return Hidden_Field
     */
    public function set_value($value)
    {
        if ($value === null) {
            return $this;
        }

        parent::set_value($value);

        return $this;
    }
}
