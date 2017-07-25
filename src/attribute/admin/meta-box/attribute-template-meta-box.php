<?php
namespace Affilicious\Attribute\Admin\Meta_Box;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Type;
use Affilicious\Attribute\Repository\Carbon\Carbon_Attribute_Template_Repository;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Meta_Box
{
    /**
     * @hook init
     * @since 0.9
     */
    public function render()
    {
        do_action('aff_admin_meta_box_before_render_detail_template_container');

        $carbon_container = Carbon_Container::make('term_meta', __('Attribute Template', 'affilicious'))
            ->show_on_taxonomy(Attribute_Template::TAXONOMY)
            ->add_fields(array(
                Carbon_Field::make('select', Carbon_Attribute_Template_Repository::TYPE, __('Type', 'affilicious'))
                    ->set_required(true)
                    ->add_options(array(
                        Type::TEXT => __('Text', 'affilicious'),
                        Type::NUMBER => __('Number', 'affilicious'),
                    )),
                Carbon_Field::make('text', Carbon_Attribute_Template_Repository::UNIT, __('Unit', 'affilicious'))
                    ->set_conditional_logic(array(
                        'relation' => 'and',
                        array(
                            'field' => Carbon_Attribute_Template_Repository::TYPE,
                            'value' => Type::NUMBER,
                            'compare' => '=',
                        )
                    )),
            ));

        $carbon_container = apply_filters('aff_admin_meta_box_render_detail_template_container', $carbon_container);

        do_action('aff_admin_meta_box_after_render_detail_template_container', $carbon_container);
    }
}
