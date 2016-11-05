<?php
namespace Affilicious\Shop\Application\Options;

use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

class Provider_Options
{
    public static $countries = array(

    );

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function render()
    {
        do_action('affilicious_options_provider_before_render');

        $amazon_tab = apply_filters('affilicious_options_provider_container_amazon_tab', array(
            Carbon_Field::make('password', 'affilicious_options_provider_container_amazon_tab_access_key_id_field', __('Access Key ID', 'affilicious')),
            Carbon_Field::make('password', 'affilicious_options_provider_container_amazon_tab_secret_access_key_field', __('Secret Access Key', 'affilicious')),
            Carbon_Field::make('select', 'affilicious_options_provider_container_amazon_tab_country_field', __('Country', 'affilicious'))
                ->add_options(array(
                    'de' => __('Germany', 'affilicious'),
                    'com' => __('America', 'affilicious'),
                    'co.uk' => __('England', 'affilicious'),
                    'ca' => __('Canada', 'affilicious'),
                    'fr' => __('France', 'affilicious'),
                    'co.jp' => __('Japan', 'affilicious'),
                    'it' => __('Italy', 'affilicious'),
                    'cn' => __('China', 'affilicious'),
                    'es' => __('Spain', 'affilicious'),
                    'in' => __('India', 'affilicious'),
                    'com.br' => __('Brazil', 'affilicious'),
                    'com.mx' => __('Mexico', 'affilicious'),
                    'com.au' => __('Australia', 'affilicious'),
                )),
            Carbon_Field::make('text', 'affilicious_options_provider_container_amazon_tab_partner_tag_field', __('Partner Tag', 'affilicious')),
        ));

        $container = Carbon_Container::make('theme_options', __('Provider', 'affilicious'))
            ->set_page_parent('affilicious')
            ->add_tab(__('Amazon', 'affilicious'), $amazon_tab);

        apply_filters('affilicious_options_provider_container', $container);
        do_action('affilicious_options_provider_after_render');
    }
}
