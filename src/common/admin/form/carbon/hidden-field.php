<?php
namespace Carbon_Fields\Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Hidden_Field extends Field
{
    /**
     * Underscore template of this field.
     *
     * @since 0.8
     */
    public function template() {
        ?>
        <input id="{{{ id }}}" type="hidden" name="{{{ name }}}" value="{{ value }}"/>
        <?php
    }

    /**
     * @inheritdoc
     * @since 0.8
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
