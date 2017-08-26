<?php
namespace Affilicious\Common\Template;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Template_Locator
{
	/**
	 * Locate the template by the given name.
	 *
	 * @since 0.9.5
	 * @param string|string[] $template_names The name(s) of the template(s) to locate.
	 * @return string|null The path to the or one of the template(s).
	 */
	public function locate($template_names)
	{
		if(!is_array($template_names)) {
			$template_names = [$template_names];
		}

		Assert_Helper::all_is_string_not_empty($template_names, __METHOD__, 'The template name has to be a string. Got: %s.', '0.9.5');

		foreach ($template_names as $template_name) {
			// Trim off any slashes from the template name.
			$template_name = ltrim($template_name, '/');

			// Find the template in the already existing paths.
			$template_dir_paths = $this->get_template_dir_paths();
			foreach ($template_dir_paths as $template_dir_path) {
				$template_path = $template_dir_path . $template_name;
				$template_path = apply_filters("aff_locate_template", $template_path, $template_name, $template_dir_path);

				if (file_exists($template_path)) {
					$template_path = apply_filters("aff_located_template", $template_path, $template_name, $template_dir_path);

					return $template_path;
				}
			}
		}

		// Template not found.
		return null;
	}

	/**
	 * Get a list of all available template directory path locations.
	 *
	 * Default paths by priority:
	 *    10. 'aff-templates' dir in the child theme.
	 *    20. 'templates' dir in the child theme.
	 *    30. 'aff-templates' dir in the parent theme.
	 *    40. 'templates' dir in the parent theme.
	 *    50. 'aff-templates' dir in the Affilicious plugin.
	 *    60. 'templates' dir in the Affilicious plugin.
	 *
	 * You can add new paths with the "aff_template_paths" filter.
	 *
	 * @since 0.9.5
	 * @return string[]
	 */
	public function get_template_dir_paths()
	{
		$file_paths = [
			10 => trailingslashit(get_stylesheet_directory()) . 'aff-templates',
			20 => trailingslashit(get_template_directory()) . 'aff-templates',
			100 => trailingslashit(AFFILICIOUS_ROOT_PATH) . 'templates',
		];

		$file_paths = apply_filters('aff_template_paths', $file_paths);
		Assert_Helper::all_is_type_of($file_paths, 'string', __METHOD__, 'Every template path has to be a string. Got: %s', '0.9.5');

		// Sort by priority.
		ksort($file_paths, SORT_NUMERIC);

		// Finalize the paths with a trailing slash.
		$file_paths = array_map('trailingslashit', $file_paths);

		return $file_paths;
	}
}
