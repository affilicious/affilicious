<?php
namespace Affilicious\Product\Customizer;

use Affilicious\Common\Customizer\Abstract_Customizer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Universal_Box_Customizer extends Abstract_Customizer
{
	/**
	 * @inheritdoc
	 * @since 0.9.10
	 */
	public function get_name()
	{
		return 'aff_customizer_universal_box';
	}

    /**
     * @inheritdoc
     * @since 0.9.10
     */
    protected function build()
    {
        $panels = [];

        $panels['aff_universal_box'] = [
            'title' => __('Universal box', 'affilicious'),
            'priority' => 100,
            'sections' => [],
        ];

        $panels = $this->build_reviews($panels);

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
            'priority' => 10,
            'settings' => [],
        );

        $panels['aff_universal_box']['sections']['reviews']['settings']['background_color_even'] = array(
            'type' => 'color',
            'label' => __('Background Color (Even)', 'affilicious'),
            'default' => '#f0f0f0',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => 'sanitize_hex_color',
            'css' => [
                [
                    'selector' => '.aff_universal_box-vertical-responsive .aff_universal_box-column-field:not(.aff_universal_box-column-field-highlighted):nth-child(odd)',
                    'property' => 'background-color',
                ],
            ],
        );

        return $panels;
    }
}
