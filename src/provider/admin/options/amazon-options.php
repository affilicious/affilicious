<?php
namespace Affilicious\Provider\Admin\Options;

use Affilicious\Common\Helper\Template_Helper;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Credentials;
use Affilicious\Provider\Validator\Credentials_Validator_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

class Amazon_Options
{
    const IMPORT = 'affilicious_options_amazon_container_credentials_tab_import_field';
    const VALIDATION_STATUS = 'affilicious_options_amazon_container_credentials_tab_validation_status_field';
    const ACCESS_KEY = 'affilicious_options_amazon_container_credentials_tab_access_key_field';
    const SECRET_KEY = 'affilicious_options_amazon_container_credentials_tab_secret_key_field';
    const COUNTRY = 'affilicious_options_amazon_container_credentials_tab_country_field';
    const ASSOCIATE_TAG = 'affilicious_options_amazon_container_credentials_tab_associate_tag_field';
    const THUMBNAIL_UPDATE_INTERVAL = 'affilicious_options_amazon_container_updates_tab_thumbnail_update_interval_field';
    const IMAGE_GALLERY_UPDATE_INTERVAL = 'affilicious_options_amazon_container_updates_tab_image_gallery_update_interval_field';
    const PRICE_UPDATE_INTERVAL = 'affilicious_options_amazon_container_updates_tab_price_update_interval_field';
    const OLD_PRICE_UPDATE_INTERVAL = 'affilicious_options_amazon_container_updates_tab_old_price_update_interval_field';
    const RATING_UPDATE_INTERVAL = 'affilicious_options_amazon_container_updates_tab_rating_update_interval_field';
    const VOTES_UPDATE_INTERVAL = 'affilicious_options_amazon_container_updates_tab_votes_update_interval_field';
    const AVAILABILITY_UPDATE_INTERVAL = 'affilicious_options_amazon_container_updates_tab_availability_update_interval_field';

    /**
     * @var Credentials_Validator_Interface
     */
    private $amazon_credentials_validator;

    /**
     * @since 0.9
     * @param Credentials_Validator_Interface $amazon_credentials_validator
     */
    public function __construct(Credentials_Validator_Interface $amazon_credentials_validator)
    {
        $this->amazon_credentials_validator = $amazon_credentials_validator;
    }

    /**
     * Render the amazon options.
     *
     * @hook init
     * @since 0.9
     */
    public function render()
    {
        do_action('aff_admin_options_before_render_amazon_container');

        $container = Carbon_Container::make('theme_options', __('Amazon', 'affilicious'))
            ->set_page_parent('affilicious')
            ->add_tab(__('Credentials', 'affilicious'), $this->get_credentials_fields())
            ->add_tab(__('Updates', 'affilicious'), $this->get_updates_fields());

        $container = apply_filters('aff_admin_options_render_amazon_container', $container);

        do_action('aff_admin_options_after_render_amazon_container', $container);
    }

    /**
     * Get the credentials fields.
     *
     * @since 0.9
     * @return array
     */
    private function get_credentials_fields()
    {
        $fields = array(
            Carbon_Field::make('html', self::VALIDATION_STATUS)
                ->set_html($this->get_validation_notice()),
            Carbon_Field::make('text', self::ACCESS_KEY, __('Access Key', 'affilicious'))
                ->set_help_text(__('The access key is used to identify you as an API user.', 'affilicious')),
            Carbon_Field::make('password', self::SECRET_KEY, __('Secret Key', 'affilicious'))
                ->set_help_text(__('The secret key is used like a password to sign your API requests.', 'affilicious')),
            Carbon_Field::make('select', self::COUNTRY, __('Country', 'affilicious'))
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
                ->set_help_text(__('The country has to match the locale of your Amazon account. ', 'affilicious')),
            Carbon_Field::make('text', self::ASSOCIATE_TAG, __('Associate Tag', 'affilicious'))
                ->set_help_text(__('Amazon uses this ID to credit an associate for a sale.', 'affilicious'))
        );

        return apply_filters('aff_admin_options_render_amazon_container_credentials_fields', $fields);
    }

    /**
     * Get the updates fields.
     *
     * @since 0.9
     * @return array
     */
    private function get_updates_fields()
    {
        $fields = array(
            Carbon_Field::make('select', self::THUMBNAIL_UPDATE_INTERVAL, __('Thumbnail Update Interval', 'affilicious'))
                ->add_options(array(
                    'hourly' => __('Hourly', 'affilicious'),
                    'twicedaily' => __('Twice Daily', 'affilicious'),
                    'daily' => __('Daily', 'affilicious'),
                    'none' => __('No Updates', 'affilicious'),
                ))
                ->set_help_text(__('The automatic update interval for the product thumbnails.', 'affilicious')),
            Carbon_Field::make('select', self::IMAGE_GALLERY_UPDATE_INTERVAL, __('Image Gallery Update Interval', 'affilicious'))
                ->add_options(array(
                    'hourly' => __('Hourly', 'affilicious'),
                    'twicedaily' => __('Twice Daily', 'affilicious'),
                    'daily' => __('Daily', 'affilicious'),
                    'none' => __('No Updates', 'affilicious'),
                ))
                ->set_help_text(__('The automatic update interval for the product image galleries.', 'affilicious')),
            Carbon_Field::make('select', self::PRICE_UPDATE_INTERVAL, __('Price Update Interval', 'affilicious'))
                ->add_options(array(
                    'hourly' => __('Hourly', 'affilicious'),
                    'twicedaily' => __('Twice Daily', 'affilicious'),
                    'daily' => __('Daily', 'affilicious'),
                    'none' => __('No Updates', 'affilicious'),
                ))
                ->set_help_text(__('The automatic update interval for the shop prices in the products.', 'affilicious')),
            Carbon_Field::make('select', self::OLD_PRICE_UPDATE_INTERVAL, __('Old Price Update Interval', 'affilicious'))
                ->add_options(array(
                    'hourly' => __('Hourly', 'affilicious'),
                    'twicedaily' => __('Twice Daily', 'affilicious'),
                    'daily' => __('Daily', 'affilicious'),
                    'none' => __('No Updates', 'affilicious'),
                ))
                ->set_help_text(__('The automatic update interval for the old shop prices in the products.', 'affilicious')),
            Carbon_Field::make('select', self::AVAILABILITY_UPDATE_INTERVAL, __('Availability Update Interval', 'affilicious'))
                ->add_options(array(
                    'hourly' => __('Hourly', 'affilicious'),
                    'twicedaily' => __('Twice Daily', 'affilicious'),
                    'daily' => __('Daily', 'affilicious'),
                    'none' => __('No Updates', 'affilicious'),
                ))
                ->set_help_text(__('The automatic update interval for the shop availabilities in the products.', 'affilicious')),
        );

        return apply_filters('aff_admin_options_render_amazon_container_updates_fields', $fields);
    }

    /**
     * Get the validation notice for Amazon.
     *
     * @since 0.9
     * @return bool
     */
    protected function get_validation_notice()
    {
        $valid = $this->check_validation_status();

        if($valid) {
            $notice = Template_Helper::stringify('notices/success-notice', array(
                'message' => __('<b>The credentials are valid!</b> A connection to the Amazon Product Advertising API was successfully established.', 'affilicious')
            ));
        } else {
            $notice = Template_Helper::stringify('notices/error-notice', array(
                'message' => __('<b>The credentials are invalid!</b> Failed to connect to the Amazon Product Advertising API.', 'affilicious')
            ));
        }

        return $notice;
    }

    /**
     * Check the validation status of the credentials for Amazon.
     *
     * @since 0.9
     * @return bool
     */
    protected function check_validation_status()
    {
        // Don't make unnecessary Amazon API calls.
        if(!isset($_GET['page']) || !($_GET['page'] === 'crbn-amazon.php')) {
            return false;
        }

        $access_key = carbon_get_theme_option(Amazon_Options::ACCESS_KEY);
        $secret_key = carbon_get_theme_option(Amazon_Options::SECRET_KEY);
        $country = carbon_get_theme_option(Amazon_Options::COUNTRY);
        $associate_tag = carbon_get_theme_option(Amazon_Options::ASSOCIATE_TAG);

        if(empty($access_key) || empty($secret_key) || empty($country) || empty($associate_tag)) {
            return false;
        }

        $credentials = new Credentials(array(
            Amazon_Provider::ACCESS_KEY => $access_key,
            Amazon_Provider::SECRET_KEY => $secret_key,
            Amazon_Provider::COUNTRY => $country,
            Amazon_Provider::ASSOCIATE_TAG => $associate_tag
        ));

        $result = $this->amazon_credentials_validator->validate($credentials);
        if($result instanceof \WP_Error) {
        	return false;
        }

        return true;
    }
}
