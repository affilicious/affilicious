<?php
namespace Carbon_Fields\Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Tags_Field extends Field
{
    /**
     * @var string[]
     */
    private $options;

    /**
     * @var int
     */
    private $max_items = 100;

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function to_json($load)
    {
        $field_data = parent::to_json($load);

        $field_data['tags'] = $this->options;
        $field_data['maxItems'] = $this->max_items;

        return $field_data;
    }

    /**
     * @param int $max_items
     * @return $this
     */
    public function set_max_items($max_items)
    {
        $this->max_items = $max_items;

        return $this;
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
