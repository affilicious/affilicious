<?php
namespace Carbon_Fields\Field;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Password_Field extends Field
{
    /**
     * Underscore template of this field.
     */
    public function template() {
        ?>
        <input id="{{{ id }}}" type="password" name="{{{ name }}}" value="{{ value }}"/>
        <?php
    }
}
