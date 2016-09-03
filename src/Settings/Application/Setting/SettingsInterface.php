<?php
namespace Affilicious\Settings\Application\Setting;

interface SettingsInterface
{
	/**
	 * Render the settings into the admin area
	 *
	 * @since 0.5
	 */
	public function render();

	/**
	 * Apply the saved settings
	 *
	 * @since 0.5
	 */
	public function apply();
}
