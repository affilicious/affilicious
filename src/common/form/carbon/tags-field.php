<?php
namespace Carbon_Fields\Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Tags_Field extends Field
{
    private $options;

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function to_json($load)
    {
        $field_data = parent::to_json($load);

        $field_data['tags'] = $this->options;

        return $field_data;
    }

    /**
     * @since 0.9
     * @param array $options
     * @return $this
     */
    public function add_tags($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Underscore template of this field.
     *
     * @since 0.7.1
     */
    public function template()
    {
        ?>
        <input id="{{{ id }}}" class="aff-tags <# if(tags) { #>aff-tags-predefined<# } else { #> aff-tags-custom <# } #>" type="text" name="{{{ name }}}" value="{{ value }}"/>
        <?php
    }
}
