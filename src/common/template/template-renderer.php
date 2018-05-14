<?php
namespace Affilicious\Common\Template;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.5
 */
class Template_Renderer
{
	/**
	 * @since 0.9.5
	 * @var Template_Locator
	 */
	protected $template_locator;

	/**
	 * @since 0.9.5
	 * @param Template_Locator $template_locator
	 */
	public function __construct(Template_Locator $template_locator)
	{
		$this->template_locator = $template_locator;
	}

	/**
	 * Render the template immediately.
	 *
	 * @since 0.9.5
	 * @param string $name The name for the template.
	 * @param array $params The variables for the template. Default: empty array.
	 * @param bool $require Whether to require or require. Default: true.
	 */
	public function render($name, $params = [], $require = true)
	{
		Assert_Helper::is_string_not_empty($name, __METHOD__, 'The template name has to be a non empty string. Got: %s', '0.9.5');

		do_action('aff_before_render_template', $name, $name);
		do_action("aff_before_render_template_{$name}", $name, $name);

		// Locate the template file in the default templates paths.
		$located_path = $this->template_locator->locate("{$name}.php");
		if($located_path === null) {
			return;
		}

		// The params are extracted into simple variables which can be used in the template.
		$params = apply_filters('aff_render_template_params', $params, $name);
		$params = apply_filters("aff_render_template_params_{$name}", $params);
		extract($params);

		// Require or include the template once.
		if($require) {
			/** @noinspection PhpIncludeInspection */
			require($located_path);
		} else {
			/** @noinspection PhpIncludeInspection */
			include($located_path);
		}

		do_action('aff_after_render_template', $name, $name);
		do_action("aff_after_render_template_{$name}", $name, $name);
	}

	/**
	 * Buffers the rendered template into a string.
	 *
	 * @since 0.9.5
	 * @param string $name The name for the template.
	 * @param array $params The variables for the template. Default: empty array.
	 * @param bool $require Whether to require or require. Default: true.
	 * @return string The buffered and rendered template.
	 */
	public function stringify($name, $params = [], $require = true)
	{
		// Every output is converted to a simple string
		ob_start();

		$this->render($name, $params, $require);

		// Get the rendered template output.
		$content = ob_get_clean();

		return $content;
	}
}
