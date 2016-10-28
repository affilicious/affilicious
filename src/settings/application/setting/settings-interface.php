<?php
namespace Affilicious\Settings\Application\Setting;

interface Settings_Interface
{
	/**
	 * Render the settings into the admin area
	 *
	 * @since 0.6
	 */
	public function render();

	/**
	 * Apply the saved settings
	 *
	 * @since 0.6
	 */
	public function apply();
}
