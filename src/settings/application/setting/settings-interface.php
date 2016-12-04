<?php
namespace Affilicious\Settings\Application\Setting;

/**
 * TODO: Remove this class in the beta
 * @deprecated
 */
interface Settings_Interface
{
	/**
	 * Render the settings into the admin area
	 *
     * @deprecated
	 * @since 0.6
	 */
	public function render();

	/**
	 * Apply the saved settings
	 *
     * @deprecated
	 * @since 0.6
	 */
	public function apply();
}
