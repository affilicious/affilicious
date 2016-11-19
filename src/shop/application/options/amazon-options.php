<?php
namespace Affilicious\Shop\Application\Options;

use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

class Amazon_Options
{
    /**
     * @since 0.6
     */
    public function render()
    {
        do_action('affilicious_options_amazon_before_render');

        $credentials_tab = apply_filters('affilicious_options_amazon_container_credentials_tab', array(
            Carbon_Field::make('password', 'affilicious_options_amazon_container_credentials_tab_access_key_id_field', __('Access Key ID', 'affilicious'))
                ->set_required(true),
            Carbon_Field::make('password', 'affilicious_options_amazon_container_credentials_tab_secret_access_key_field', __('Secret Access Key', 'affilicious'))
                ->set_required(true),
            Carbon_Field::make('select', 'affilicious_options_amazon_container_credentials_tab_country_field', __('Country', 'affilicious'))
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
                ))
                ->set_required(true),
            Carbon_Field::make('text', 'affilicious_options_amazon_container_credentials_tab_partner_tag_field', __('Partner Tag', 'affilicious'))
                ->set_required(true),
        ));

        $container = Carbon_Container::make('theme_options', __('Amazon', 'affilicious'))
            ->set_page_parent('affilicious')
            ->add_tab(__('Credentials', 'affilicious'), $credentials_tab);

        apply_filters('affilicious_options_amazon_container', $container);
        do_action('affilicious_options_amazon_after_render');
    }
}
