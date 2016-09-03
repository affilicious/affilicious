<?php
namespace Affilicious\Settings\Application\Setting;

use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

class AffiliciousSettings implements SettingsInterface
{
	/**
	 * @inheritdoc
	 * @since 0.5
	 */
	public function render()
	{
		do_action('affilicious_settings_affilicious_render');

		$scriptsTabs = apply_filters('affilicious_settings_affilicious_scripts_fields', array(
			CarbonField::make('header_scripts', 'affilicious_settings_custom_css', __('Custom CSS', 'affilicious')),
			CarbonField::make('footer_scripts', 'affilicious_settings_custom_js', __('Custom JS', 'affilicious')),
		));

		$container = CarbonContainer::make('theme_options', 'Affilicious')
	       ->set_icon('dashicons-admin-generic')
	       ->add_tab(__('Scripts', 'affilicious'), $scriptsTabs);

		apply_filters('affilicious_settings_affilicious_container', $container);
	}

	/**
	 * @inheritdoc
	 * @since 0.5
	 */
	public function apply()
	{
		do_action('affilicious_settings_affilicious_apply');
	}
}
