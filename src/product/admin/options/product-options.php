<?php
namespace Affilicious\Product\Admin\Options;

use Affilicious\Product\Helper\Universal_Mode_Helper;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

class Product_Options
{
	const LINK_WHAT_IS_TAXONOMY = 'https://codex.wordpress.org/Taxonomies';
	const LINK_RESERVED_TERMS = 'https://codex.wordpress.org/Function_Reference/register_taxonomy#Reserved_Terms';

	/**
	 * @hook init
	 * @since 0.9
	 */
	public function render()
	{
		do_action('aff_admin_options_before_render_products_container');

		$container = Carbon_Container::make('theme_options',  __('Products', 'affilicious'))
           ->set_page_parent('affilicious')
           ->add_tab(__('General', 'affilicious'), $this->get_general_fields())
           ->add_tab(__('Universal box', 'affilicious'), $this->get_universal_box_fields())
           ->add_tab(__('Custom Taxonomies', 'affilicious'), $this->get_custom_taxonomies_fields());

        $container = apply_filters('aff_admin_options_render_products_container', $container);

        do_action('aff_admin_options_after_render_products_container', $container);
	}

    /**
     * Get the general fields.
     *
     * @since 0.9
     * @return array
     */
	protected function get_general_fields()
    {
        $fields = array(
            Carbon_Field::make('text', 'affilicious_options_product_container_general_tab_slug_field', __('Slug', 'affilicious'))
                ->help_text(__('Used as the slug for a nicer product URL (eg "http://example.com/products/xyz"), where "products" in the middle part of the product URL is the default. If you want to translate the slug into your language instead of "products", you have to write the new slug into this input field.', 'affilicious')),
        );

        return apply_filters('aff_admin_options_render_products_container_general_fields', $fields);
    }

	/**
	 * Get the universal box fields.
	 *
	 * @since 0.9
	 * @return array
	 */
    protected function get_universal_box_fields()
    {
    	if(!Universal_Mode_Helper::is_enabled()) {
    		return [];
	    }

		$fields = [
			Carbon_Field::make('checkbox', 'affilicious_options_product_container_universal_box_tab_disabled_field', __('Disabled', 'affilicious'))
                ->help_text(__('Whether to disable the universal box or not.', 'affilicious')),
			Carbon_Field::make('select', 'affilicious_options_product_container_universal_box_tab_position_field', __('Position', 'affilicious'))
	            ->help_text(__('Whether the position of the universal box is above or below the content.', 'affilicious'))
				->add_options([
					'above' => __('Above the content', 'affilicious'),
					'below' => __('Below the content', 'affilicious'),
				])
	            ->set_required(true),
		];

	    return apply_filters('aff_admin_options_render_products_container_universal_box_fields', $fields);
    }

    /**
     * Get the custom taxonomies fields.
     *
     * @since 0.9
     * @return array
     */
    protected function get_custom_taxonomies_fields()
    {
        $fields = array(
            Carbon_Field::make('html', 'affilicious_options_product_container_taxonomies_tab_description_field')
                ->set_html(sprintf('<p>%s</p>', sprintf(__('Create custom taxonomies to group products together. See this <a href="%s">link</a> for a better description.', 'affilicious'), self::LINK_WHAT_IS_TAXONOMY))),
            Carbon_Field::make('complex', 'affilicious_options_product_container_taxonomies_tab_taxonomies_field', __('Taxonomies', 'affilicious'))
                ->add_fields(array(
                    Carbon_Field::make('text', 'taxonomy', __('Taxonomy', 'affilicious'))
                        ->help_text(sprintf(
                            __('The database name of the taxonomy like "your_category". Name should only contain lowercase letters and the underscore character, and not be more than 32 characters long. Care should be used in selecting a taxonomy name so that it does not conflict with other taxonomies, post types, and reserved Wordpress public and protected query variables. A complete list of those is described in the <a href="%s">Reserved Terms</a> section.', 'affilicious'),
                            self::LINK_RESERVED_TERMS
                        ))
                        ->set_required(true),
                    Carbon_Field::make('text', 'slug', __('Slug', 'affilicious'))
                        ->help_text(__('Used as pretty permalink text for your URL like "http://example.com/your-categories/product-name".', 'affilicious'))
                        ->set_required(true),
                    Carbon_Field::make('text', 'singular_name', __('Singular Name', 'affilicious'))
	                    ->help_text(__('The singular name like "Your category".', 'affilicious'))
                        ->set_required(true),
                    Carbon_Field::make('text', 'plural_name', __('Plural Name', 'affilicious'))
	                    ->help_text(__('The plural name like "Your categories".', 'affilicious'))
                        ->set_required(true),
                ))
        );

        return apply_filters('aff_admin_options_render_products_container_custom_taxonomies_fields', $fields);
    }
}
