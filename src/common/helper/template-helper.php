<?php
namespace Affilicious\Common\Helper;

use Affilicious\Common\Template\Template_Renderer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.5
 */
class Template_Helper
{
	/**
	 * Render the template immediately.
	 *
	 * @since 0.9.5
	 * @param string $name The name for the template.
	 * @param array $params The variables for the template. Default: empty array.
	 * @param bool $require Whether to require or require. Default: true.
	 */
	public static function render($name, $params = [], $require = true)
	{
		/** @var Template_Renderer $template_renderer */
		$template_renderer = \Affilicious::get('affilicious.common.template.renderer');

		$template_renderer->render($name, $params, $require);
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
	public static function stringify($name, $params = [], $require = true)
	{
		/** @var Template_Renderer $template_renderer */
		$template_renderer = \Affilicious::get('affilicious.common.template.renderer');

		return $template_renderer->stringify($name, $params, $require);
	}
}
