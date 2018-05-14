<?php
namespace Affilicious\Product\Customizer;

use Affilicious\Common\Customizer\Abstract_Customizer;
use Affilicious\Product\Helper\Universal_Mode_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.10
 */
class Universal_Box_Customizer extends Abstract_Customizer
{
	/**
	 * @inheritdoc
	 * @since 0.9.10
	 */
	public function get_name()
	{
		return 'aff_customizer_product_universal_box';
	}

	/**
	 * @inheritdoc
	 * @since 0.9.10
	 */
	public function get_stylesheet_handle()
	{
		return 'aff-universal-box';
	}

    /**
     * @inheritdoc
     * @since 0.9.10
     */
    protected function build()
    {
	    if(!Universal_Mode_Helper::is_enabled()) {
		    return [];
	    }

        $panels = [];

        $panels['aff_universal_box'] = [
            'title' => __('Universal box', 'affilicious'),
            'priority' => 30,
            'sections' => [],
        ];

        $panels = $this->build_general($panels);
        $panels = $this->build_title($panels);
        $panels = $this->build_tags($panels);
	    $panels = $this->build_reviews($panels);
	    $panels = $this->build_price($panels);
        $panels = $this->build_attribute_choices($panels);
        $panels = $this->build_details($panels);
        $panels = $this->build_shops($panels);
        $panels = $this->build_related_products_and_accessories($panels);

        return $panels;
    }

	/**
	 * Build the product general section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_general(array $panels)
	{
		$panels['aff_universal_box']['sections']['general'] = array(
			'title' => __('General', 'affilicious'),
			'priority' => 10,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['general']['settings']['box_background_color'] = array(
			'label'     => __('Box Background Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#fff',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['general']['settings']['box_border_color'] = array(
			'label'     => __('Box Border Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#eee',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box",
					'property' => 'border-color',
				),
				array(
					'selector' => ".aff-product-universal-box-column",
					'property' => 'border-bottom-color',
				),
				array(
					'selector' => ".aff-product-universal-box-column-half-width",
					'property' => 'border-right',
				),
			)
		);

		return $panels;
	}

	/**
	 * Build the product title section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_title(array $panels)
	{
		$panels['aff_universal_box']['sections']['title'] = array(
			'title' => __('Title', 'affilicious'),
			'priority' => 20,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['color'] = array(
			'label'     => __('Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-title",
					'property' => 'color',
				),
			)
		);

		return $panels;
	}

	/**
	 * Build the tags section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_tags(array $panels)
	{
		$panels['aff_universal_box']['sections']['tags'] = array(
			'title' => __('Tags', 'affilicious'),
			'priority' => 30,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['tags']['settings']['tag_text_color'] = array(
			'label'     => __('Tag Text Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#fff',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['tags']['settings']['tag_text_color_hover'] = array(
			'label'     => __('Tag Text Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#fff',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item:hover",
					'property' => 'color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item:focus",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['tags']['settings']['tag_background_color'] = array(
			'label'     => __('Tag Background Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#3bafda',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['tags']['settings']['tag_background_color_hover'] = array(
			'label'     => __('Tag Background Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#379ac3',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item:hover",
					'property' => 'background-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item:focus",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['tags']['settings']['tag_border_color'] = array(
			'label'     => __('Tag Background Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#379ac3',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item",
					'property' => 'border-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['tags']['settings']['tag_border_color_hover'] = array(
			'label'     => __('Tag Background Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#379ac3',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item:hover",
					'property' => 'border-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-tags-item:focus",
					'property' => 'border-color',
				),
			)
		);

		return $panels;
	}

	/**
	 * Build the reviews section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_reviews(array $panels)
	{
		$panels['aff_universal_box']['sections']['reviews'] = array(
			'title' => __('Reviews', 'affilicious'),
			'priority' => 40,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['reviews']['settings']['rating_color'] = array(
			'label'     => __('Rating Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ffd055',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-review-rating-star path",
					'property' => 'fill',
				),
			)
		);

		$panels['aff_universal_box']['sections']['reviews']['settings']['votes_color'] = array(
			'label'     => __('Votes Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#888',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-review-votes",
					'property' => 'color',
				),
			)
		);

		return $panels;
	}

	/**
	 * Build the price section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_price(array $panels)
	{
		$panels['aff_universal_box']['sections']['price'] = array(
			'title' => __('Price', 'affilicious'),
			'priority' => 50,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['price']['settings']['price_color'] = array(
			'label'     => __('Price Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#2fa32f',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-price-current",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['price']['settings']['old_price_color'] = array(
			'label'     => __('Old Price Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ff2320',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-price-old",
					'property' => 'color',
				),
			)
		);

		return $panels;
	}

	/**
	 * Build the attribute choices section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_attribute_choices(array $panels)
	{
		$panels['aff_universal_box']['sections']['attribute_choices'] = array(
			'title' => __('Attribute choices', 'affilicious'),
			'priority' => 60,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['attribute_choices']['settings']['choice-background-color'] = array(
			'label'     => __('Attribute Choice Background Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice:not(.selected)",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['attribute_choices']['settings']['choice-background-color-hover'] = array(
			'label'     => __('Attribute Choice Background Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice:not(.selected):hover",
					'property' => 'background-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice:not(.selected):focus",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['attribute_choices']['settings']['choice-background-color-selected'] = array(
			'label'     => __('Attribute Choice Background Color (Selected)', 'affilicious'),
			'type'      => 'color',
			'default'   => '',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice.selected",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['attribute_choices']['settings']['choice-border-color'] = array(
			'label'     => __('Attribute Choice Border Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#999',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice:not(.selected)",
					'property' => 'border-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['attribute_choices']['settings']['choice-border-color-hover'] = array(
			'label'     => __('Attribute Choice Border Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#333',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice:not(.selected):hover",
					'property' => 'border-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice:not(.selected):focus",
					'property' => 'border-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['attribute_choices']['settings']['choice-border-color-selected'] = array(
			'label'     => __('Attribute Choice Border Color (Selected)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#000',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-attributes-choice.selected",
					'property' => 'border-color',
				),
			)
		);

		return $panels;
	}

	/**
	 * Build the details section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
    protected function build_details(array $panels)
    {
	    $panels['aff_universal_box']['sections']['details'] = array(
		    'title' => __('Details', 'affilicious'),
		    'priority' => 70,
		    'settings' => [],
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['background_color_odd'] = array(
		    'label'     => __('Background Color (Odd)', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '#fff',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item:nth-child(odd)",
				    'property' => 'background-color',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['background_color_even'] = array(
		    'label'     => __('Background Color (Even)', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '#fff',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item:nth-child(even)",
				    'property' => 'background-color',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['name_color'] = array(
		    'label'     => __('Name Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item-name",
				    'property' => 'color',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['value_color'] = array(
		    'label'     => __('Value Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item-value",
				    'property' => 'color',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['file_link_color'] = array(
		    'label'     => __('File Link Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item-value-file a",
				    'property' => 'color',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['file_link_color_hover'] = array(
		    'label'     => __('File Link Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item-value-file a:hover",
				    'property' => 'color',
			    ),
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item-value-file a:focus",
				    'property' => 'color',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['boolean_check_color'] = array(
		    'label'     => __('Boolean Check Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '#5CB85C',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item-value-boolean-check path",
				    'property' => 'fill',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['boolean_cross_color'] = array(
		    'label'     => __('Boolean Cross Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '#D9534F',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item-value-boolean-cross path",
				    'property' => 'fill',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['separator_color'] = array(
		    'label'     => __('Separator Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '#eee',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-item td",
				    'property' => 'border-bottom-color',
			    ),
		    )
	    );

	    $panels['aff_universal_box']['sections']['details']['settings']['no_details'] = array(
		    'label'     => __('No Details Color', 'affilicious'),
		    'type'      => 'color',
		    'default'   => '#888',
		    'css' => array(
			    array(
				    'selector' => ".aff-product-universal-box .aff-product-details-none",
				    'property' => 'color',
			    ),
		    )
	    );

    	return $panels;
    }

	/**
	 * Build the shops section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_shops(array $panels)
	{
		$panels['aff_universal_box']['sections']['shops'] = array(
			'title' => __('Shops', 'affilicious'),
			'priority' => 80,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['price_color'] = array(
			'label'     => __('Price Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#2fa32f',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-price-current",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['old_price_color'] = array(
			'label'     => __('Old Price Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ff2320',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-price-old",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['price_indication_color'] = array(
			'label'     => __('Price Indication Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#888',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-price-indication",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_buy_text'] = array(
			'label'     => __('Buy Text', 'affilicious'),
			'description' => __('Use %s as a placeholder for the shop name.', 'affilicious'),
			'type'      => 'text',
			'default'   => __('Buy now at %s', 'affilicious'),
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_buy_background_color'] = array(
			'label'     => __('Buy Background Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ff8c14',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_buy_background_color_hover'] = array(
			'label'     => __('Buy Background Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ed7709',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy:hover",
					'property' => 'background-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy:focus",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_buy_border_color'] = array(
			'label'     => __('Buy Border Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ff870a',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy",
					'property' => 'border-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_buy_border_color_hover'] = array(
			'label'     => __('Buy Border Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ed7709',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy:hover",
					'property' => 'border-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy:focus",
					'property' => 'border-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_buy_text_color'] = array(
			'label'     => __('Buy Text Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ffffff',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_buy_text_color_hover'] = array(
			'label'     => __('Buy Text Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ffffff',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy:hover",
					'property' => 'color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-buy:focus",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_not_available_text'] = array(
			'label'     => __('Not Available Text', 'affilicious'),
			'description' => __('Use %s as a placeholder for the shop name.', 'affilicious'),
			'type'      => 'text',
			'default'   => __('Unfortunately not available.', 'affilicious'),
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_not_available_background_color'] = array(
			'label'     => __('Not Available Background Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_not_available_background_color_hover'] = array(
			'label'     => __('Not Available Background Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ddd',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available:hover",
					'property' => 'background-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available:focus",
					'property' => 'background-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_not_available_border_color'] = array(
			'label'     => __('Not Available Border Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ddd',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available",
					'property' => 'border-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_not_available_border_color_hover'] = array(
			'label'     => __('Not Available Border Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#ddd',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available:hover",
					'property' => 'border-color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available:focus",
					'property' => 'border-color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_not_available_text_color'] = array(
			'label'     => __('Not Available Text Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#000',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['button_not_available_text_color_hover'] = array(
			'label'     => __('Not Available Text Color (Hover)', 'affilicious'),
			'type'      => 'color',
			'default'   => '#000',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available:hover",
					'property' => 'color',
				),
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-button-not-available:focus",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['updated_at_indication_color'] = array(
			'label'     => __('Updated At Indication Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#888',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item-indication-updated-at",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['shops']['settings']['separator_color'] = array(
			'label'     => __('Separator color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#eee;',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-shops-item",
					'property' => 'border-bottom-color',
				),
			)
		);

		return $panels;
	}

	/**
	 * Build the related products & accessories section.
	 *
	 * @since 0.9.10
	 * @param array $panels
	 * @return array
	 */
	protected function build_related_products_and_accessories(array $panels)
	{
		$panels['aff_universal_box']['sections']['related_products_and_accessories'] = array(
			'title' => __('Related products & accessories', 'affilicious'),
			'priority' => 90,
			'settings' => [],
		);

		$panels['aff_universal_box']['sections']['related_products_and_accessories']['settings']['title_color'] = array(
			'label'     => __('Title Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-related-title",
					'property' => 'color',
				),
			)
		);

		$panels['aff_universal_box']['sections']['related_products_and_accessories']['settings']['item_border_color'] = array(
			'label'     => __('Item Border Color', 'affilicious'),
			'type'      => 'color',
			'default'   => '#eee',
			'css' => array(
				array(
					'selector' => ".aff-product-universal-box .aff-product-related-item",
					'property' => 'border-color',
				),
			)
		);

		return $panels;
	}
}
