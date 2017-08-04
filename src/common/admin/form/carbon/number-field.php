<?php
namespace Carbon_Fields\Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Number_Field extends Field
{
    /**
     * Underscore template of this field.
     */
    public function template() {
        ?>
        <input id="{{{ id }}}" type="number" name="{{{ name }}}" value="{{ value }}" class="regular-text" step="any"/>
        <?php
    }
}
