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
		CarbonContainer::make('theme_options', 'Affilicious')
	       ->set_icon('dashicons-admin-generic')
	       ->add_tab(__('Scripts', 'affilicious'), array(
	           CarbonField::make('header_scripts', 'affilicious_settings_custom_css', __('Custom CSS', 'affilicious')),
	           CarbonField::make('footer_scripts', 'affilicious_settings_custom_js', __('Custom JS', 'affilicious')),
	       ));
	}

	/**
	 * @inheritdoc
	 * @since 0.5
	 */
	public function apply()
	{

	}
}
