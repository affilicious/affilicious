<?php
namespace Carbon_Fields\Field;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Tags_Field extends Field
{
    /**
     * Underscore template of this field.
     *
     * @since 0.7.1
     */
    public function template() {
        ?>
        <input id="{{{ id }}}" class="aff-tags" type="text" name="{{{ name }}}" value="{{ value }}"/>
        <?php
    }
}
