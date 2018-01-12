<?php
namespace Affilicious\Common\Admin\Options;

use Affilicious\Common\Admin\Action\Download_Logs_Action;
use Affilicious\Common\Admin\Action\Download_System_Info_Action;
use Affilicious\Common\Admin\License\License_Manager;
use Affilicious\Common\Admin\License\License_Processor;
use Affilicious\Common\Admin\Logs\Logs;
use Affilicious\Common\Admin\System\System_Info;
use Affilicious\Common\Helper\Template_Helper;
use Affilicious\Common\Template\Template_Renderer;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

class Affilicious_Options
{
    /**
     * @var License_Manager
     */
    protected $license_manager;

    /**
     * @var License_Processor
     */
    protected $license_processor;

	/**
	 * @var System_Info
	 */
	protected $system_info;

    /**
     * @var Logs
     */
    protected $logs;

    /**
     * @var Template_Renderer
     */
    protected $template_renderer;

    /**
     * @since 0.9
     * @param License_Manager $license_manager
     * @param License_Processor $license_processor
     * @param System_Info $system_info
     * @param Logs $logs
     * @param Template_Renderer $template_renderer
     */
    public function __construct(
    	License_Manager $license_manager,
	    License_Processor $license_processor,
	    System_Info $system_info,
        Logs $logs,
        Template_Renderer $template_renderer
    ) {
        $this->license_manager = $license_manager;
        $this->license_processor = $license_processor;
	    $this->system_info = $system_info;
        $this->logs = $logs;
        $this->template_renderer = $template_renderer;
    }

    /**
	 * Render the settings into the admin area.
     *
     * @hook init
	 * @since 0.9
	 */
	public function render()
	{
		do_action('aff_admin_options_before_render_affilicious_container');

		$container = Carbon_Container::make('theme_options', 'Affilicious')
	        ->set_icon('dashicons-admin-generic')
            ->add_tab(__('Licenses', 'affilicious'), $this->get_licenses_fields())
			->add_tab(__('Scripts', 'affilicious'), $this->get_scripts_fields())
            ->add_tab(__('Notices', 'affilicious'), $this->get_notices_fields())
            ->add_tab(__('System', 'affilicious'), $this->get_system_fields())
            ->add_tab(__('Logs', 'affilicious'), $this->get_logs_fields())
		;

        $container = apply_filters('aff_admin_options_render_affilicious_container', $container);

        do_action('aff_admin_options_after_render_affilicious_container', $container);
	}

    /**
     * Get the licenses fields.
     *
     * @since 0.9.1
     * @return Carbon_Field[]
     */
	protected function get_licenses_fields()
    {
        $help_text = count($this->license_manager->get_license_handlers()) > 0
            ? sprintf(__('More add-ons and themes can be found on the official website of <a href="%s">Affilicious Theme</a>.', 'affilicious'), 'https://affilicioustheme.de')
            : sprintf(__('It looks like you haven\'t got any add-on or theme yet. Visit our official website of <a href="%s">Affilicious Theme</a> to see what you can start with.', 'affilicious'), 'https://affilicioustheme.de');

        $fields = array(
            Carbon_Field::make('html', 'affilicious_options_affilicious_container_licenses_tab_licences_field')
                ->set_html(Template_Helper::stringify('admin/licenses/licenses', array(
                    'license_manager' => $this->license_manager,
                    'license_processor' => $this->license_processor,
                )))
                ->set_help_text($help_text)
        );

        return apply_filters('aff_admin_options_render_affilicious_container_licenses_fields', $fields);
    }

    /**
     * Get the scripts fields.
     *
     * @since 0.9
     * @return Carbon_Field[]
     */
    protected function get_scripts_fields()
    {
        $fields = array(
            Carbon_Field::make('header_scripts', 'affilicious_options_affilicious_container_scripts_tab_custom_header_scripts', __('Custom Header Scripts', 'affilicious'))
                ->set_help_text(__("Add your custom header scripts like CSS or JS with the proper &lt;style&gt or &lt;script&gt tags.", 'affilicious')),
            Carbon_Field::make('footer_scripts', 'affilicious_options_affilicious_container_scripts_tab_custom_footer_scripts', __('Custom Footer Scripts', 'affilicious'))
                ->set_help_text(__("Add your custom footer scripts like Google Analytics tracking code, CSS or JS with the proper &lt;style&gt or &lt;script&gt tags.", 'affilicious')),
        );

        return apply_filters('aff_admin_options_render_affilicious_container_scripts_fields', $fields);
    }

    /**
     * Get the notices fields.
     *
     * @since 0.9.16
     * @return Carbon_Field[]
     */
    protected function get_notices_fields()
    {
        $fields = [
            Carbon_Field::make('checkbox', 'affilicious_options_affilicious_container_notices_tab_download_recommendations_disabled_field', __('Disable download recommendations', 'affilicious'))
                ->set_help_text(__("If you enable this option, download recommendation notices won't be shown after activation anymore.", 'affilicious'))
                ->set_option_value('yes')
        ];

        return apply_filters('aff_admin_options_render_affilicious_container_notices_fields', $fields);
    }

	/**
	 * Get the system fields.
	 *
	 * @since 0.9.9
	 * @return Carbon_Field[]
	 */
	protected function get_system_fields()
	{
		$fields = [
			Carbon_Field::make('html', 'affilicious_options_affilicious_container_system_tab_info_field')
                ->set_html($this->template_renderer->stringify('admin/options/affilicious/system/info', [
                    'system_info' => $this->system_info->stringify(true),
                    'download_url' => sprintf(
                        admin_url('index.php?action=%1$s&nonce=%2$s'),
                        Download_System_Info_Action::ACTION,
                        wp_create_nonce(Download_System_Info_Action::ACTION)
                    ),
                ])),
		];

		return apply_filters('aff_admin_options_render_affilicious_container_system_fields', $fields);
	}

    /**
     * Get the logs fields.
     *
     * @since 0.9.18
     * @return Carbon_Field[]
     */
	protected function get_logs_fields()
    {
        $fields = [
            Carbon_Field::make('html', 'affilicious_options_affilicious_container_logs_tab_logs_field')
                ->set_html($this->template_renderer->stringify('admin/options/affilicious/logs/logs', [
                    'logs' => $this->logs->stringify(true),
                    'download_url' => sprintf(
                        admin_url('index.php?action=%1$s&nonce=%2$s'),
                        Download_Logs_Action::ACTION,
                        wp_create_nonce(Download_Logs_Action::ACTION)
                    ),
                ])),
        ];

        return apply_filters('aff_admin_options_render_affilicious_container_logs_fields', $fields);
    }
}
