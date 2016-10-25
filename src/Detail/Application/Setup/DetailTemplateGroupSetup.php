<?php
namespace Affilicious\Detail\Application\Setup;

use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Detail\Domain\Model\Detail\Type;
use Affilicious\Detail\Domain\Model\DetailTemplateGroup;
use Affilicious\Detail\Infrastructure\Repository\Carbon\CarbonDetailTemplateGroupRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DetailTemplateGroupSetup implements SetupInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        do_action('affilicious_detail_template_group_before_init');

        $singular = __('Detail Template Group', 'affilicious');
        $plural = __('Detail Template Groups', 'affilicious');
        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $singular,
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Detail Template', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Detail Template', 'affilicious'), $singular),
            'all_items'             => __('Details', 'affilicious'),
            'add_new_item'          => sprintf(_x('Add New %s', 'Detail Template', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Detail Template', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Detail Template', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Detail Template', 'affilicious'), $singular),
            'view_item'             => sprintf(_x('View %s', 'Detail Template', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Detail Template', 'affilicious'), $singular),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Detail Template', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded To This %s', 'Detail Template', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Detail Template', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Detail Template', 'affilicious'), $plural),
        );

        register_post_type(DetailTemplateGroup::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => false,
            'show_ui' => true,
            '_builtin' => false,
            'menu_position' => 4,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => DetailTemplateGroup::POST_TYPE,
            'supports' => array('title'),
            'show_in_menu' => 'edit.php?post_type=product',
        ));

        do_action('affilicious_detail_template_group_after_init');
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        do_action('affilicious_detail_template_group_before_render');

        $carbonContainer = CarbonContainer::make('post_meta', __('Detail Templates', 'affilicious'))
            ->show_on_post_type(DetailTemplateGroup::POST_TYPE)
            ->add_fields(array(
                CarbonField::make('complex', CarbonDetailTemplateGroupRepository::DETAILS, __('Details', 'affilicious'))
                    //->set_static(true)
                    ->add_fields(array(
                        CarbonField::make('text', CarbonDetailTemplateGroupRepository::DETAIL_TITLE, __('Title', 'affilicious'))
                            ->set_required(true),
                        CarbonField::make('select', CarbonDetailTemplateGroupRepository::DETAIL_TYPE, __('Type', 'affilicious'))
                            ->set_required(true)
                            ->add_options(array(
                                Type::TEXT => __('Text', 'affilicious'),
                                Type::NUMBER => __('Number', 'affilicious'),
                                Type::FILE => __('File', 'affilicious'),
                            )),
                        CarbonField::make('text', CarbonDetailTemplateGroupRepository::DETAIL_UNIT, __('Unit', 'affilicious'))
                            ->set_conditional_logic(array(
                                'relation' => 'AND',
                                array(
                                    'field' => CarbonDetailTemplateGroupRepository::DETAIL_TYPE,
                                    'value' => Type::NUMBER,
                                    'compare' => '=',
                                )
                            )),
                        CarbonField::make('text', CarbonDetailTemplateGroupRepository::DETAIL_HELP_TEXT, __('Help Text', 'affilicious'))
                    ))
                    ->set_header_template('
                        <# if (' . CarbonDetailTemplateGroupRepository::DETAIL_TITLE . ') { #>
                            {{ ' . CarbonDetailTemplateGroupRepository::DETAIL_TITLE . ' }}
                        <# } #>
                    ')
            ));

        apply_filters('affilicious_detail_template_group_render_detail_templates_container', $carbonContainer);
        do_action('affilicious_detail_template_group_after_render');
    }

    /**
     * Add a column header for the details
     *
     * @since 0.6
     * @param array $defaults
     * @return array
     */
    public function columnsHead($defaults)
    {
        $defaults['details'] = __('Details');

        return $defaults;
    }

    /**
     * Add a column for the details
     *
     * @since 0.6
     * @param string $columnName
     * @param int $postId
     */
    public function columnsContent($columnName, $postId)
    {
        if ($columnName == 'details') {
            $detailTemplates = carbon_get_post_meta($postId, CarbonDetailTemplateGroupRepository::DETAILS, 'complex');
            if(!empty($detailTemplates)) {
                $titles = array_map(function($detailTemplate) {
                    return $detailTemplate[CarbonDetailTemplateGroupRepository::DETAIL_TITLE];
                }, $detailTemplates);

                echo implode(', ', $titles);
            }
        }
    }
}
