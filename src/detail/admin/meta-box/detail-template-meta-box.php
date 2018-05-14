<?php
namespace Affilicious\Detail\Admin\Meta_Box;

use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Repository\Carbon\Carbon_Detail_Template_Repository;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
class Detail_Template_Meta_Box
{
    /**
     * @hook init
     * @since 0.9
     */
    public function render()
    {
        do_action('aff_admin_meta_box_before_render_detail_template_container');

        $carbon_container = Carbon_Container::make('term_meta', __('Detail Template', 'affilicious'))
            ->show_on_taxonomy(Detail_Template::TAXONOMY)
            ->add_fields(array(
                Carbon_Field::make('select', Carbon_Detail_Template_Repository::TYPE, __('Type', 'affilicious'))
                    ->set_required(true)
                    ->add_options(array(
                        Type::TEXT => __('Text', 'affilicious'),
                        Type::NUMBER => __('Number', 'affilicious'),
                        Type::BOOLEAN => __('Boolean', 'affilicious'),
                        Type::FILE => __('File', 'affilicious'),
                    )),
                Carbon_Field::make('text', Carbon_Detail_Template_Repository::UNIT, __('Unit', 'affilicious'))
                    ->set_conditional_logic(array(
                        'relation' => 'and',
                        array(
                            'field' => Carbon_Detail_Template_Repository::TYPE,
                            'value' => Type::NUMBER,
                            'compare' => '=',
                        )
                    )),
            ));

        $carbon_container = apply_filters('aff_admin_meta_box_render_detail_template_container', $carbon_container);

        do_action('aff_admin_meta_box_after_render_detail_template_container', $carbon_container);
    }
}
