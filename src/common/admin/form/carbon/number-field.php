<?php
namespace Carbon_Fields\Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Number_Field extends Field
{
    /**
     * Underscore template of this field.
     *
     * @since 0.8
     */
    public function template()
    {
        ?>
        <input id="{{{ id }}}" type="number" name="{{{ name }}}" value="{{ value }}" class="regular-text" step="any"/>
        <?php
    }
}
