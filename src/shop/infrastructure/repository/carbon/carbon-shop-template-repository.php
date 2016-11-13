<?php
namespace Affilicious\Shop\Infrastructure\Repository\Carbon;

use Affilicious\Common\Domain\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Repository\Carbon\Abstract_Carbon_Repository;
use Affilicious\Shop\Domain\Exception\Shop_Template_Database_Exception;
use Affilicious\Shop\Domain\Exception\Shop_Template_Not_Found_Exception;
use Affilicious\Shop\Domain\Model\Provider\Provider_Repository_Interface;
use Affilicious\Shop\Domain\Model\Shop_Template;
use Affilicious\Shop\Domain\Model\Shop_Template_Id;
use Affilicious\Shop\Domain\Model\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Carbon_Shop_Template_Repository extends Abstract_Carbon_Repository implements Shop_Template_Repository_Interface
{
    const PROVIDER = 'affilicious_shop_template_provider';

    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.7
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function store(Shop_Template $shop_template)
    {
        // _store the shop template into the database
        $default_args = $this->get_default_args($shop_template);
        $args = $this->get_args($shop_template, $default_args);
        $id = !empty($args['id']) ? wp_update_post($args) : wp_insert_post($args);

        // _the ID and the name might has changed. _update both values
        if(empty($post)) {
            $post = get_post($id, OBJECT);
            $name = new Name($post->post_name);
            $shop_template->set_id(new Shop_Template_Id($post->ID));
            $shop_template->set_name($name);
            $shop_template->set_key($name->to_key());
        }

        // _store the shop template meta
        $this->store_thumbnail($shop_template);
        $this->store_provider($shop_template);

        return $shop_template;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function delete(Shop_Template_Id $shop_template_id)
    {
        $shop = $this->find_by_id($shop_template_id);
        if($shop === null) {
            throw new Shop_Template_Not_Found_Exception($shop_template_id);
        }

        $post = wp_delete_post($shop_template_id->get_value(), false);
        if(empty($post)) {
            throw new Shop_Template_Database_Exception($shop_template_id);
        }

        $shop->set_id(null);

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function find_by_id(Shop_Template_Id $shop_template_id)
    {
        $post = get_post($shop_template_id->get_value());
        if ($post === null || $post->post_status !== 'publish') {
            return null;
        }

        if($post->post_type !== Shop_Template::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Shop_Template::POST_TYPE);
        }

        $shop = self::get_shop_template_from_post($post);
        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function find_all()
    {
        $query = new \WP_Query(array(
            'post_type' => Shop_Template::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $shops = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $shop = self::get_shop_template_from_post($query->post);
                $shops[] = $shop;
            }

            wp_reset_postdata();
        }

        return $shops;
    }

    /**
     * Convert the Carbon post into a shop template.
     *
     * @since 0.6
     * @param \WP_Post $post
     * @return Shop_Template
     * @throws Invalid_Post_Type_Exception
     */
    protected function get_shop_template_from_post(\WP_Post $post)
    {
        if($post->post_type !== Shop_Template::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Shop_Template::POST_TYPE);
        }

        // _title, _name, _key
        $title = new Title($post->post_title);
        $name = new Name($post->post_name);
        $shop_template = new Shop_template(
            $title,
            $name,
            $name->to_key()
        );

        // ID
        $shop_template->set_id(new Shop_Template_Id($post->ID));

        // _thumbnail
        $shop_template = $this->add_thumbnail($shop_template);
        $shop_template = $this->add_provider($shop_template);

        return $shop_template;
    }

    /**
     * Add the thumbnail to the shop template.
     *
     * @since 0.6
     * @param Shop_template $shop_template
     * @return Shop_Template
     */
    protected function add_thumbnail(Shop_Template $shop_template)
    {
        $thumbnail_id = get_post_thumbnail_id($shop_template->get_id()->get_value());
        if (!empty($thumbnail_id)) {
            $thumbnail = self::get_image_from_attachment_id($thumbnail_id);

            if($thumbnail !== null) {
                $shop_template->set_thumbnail($thumbnail);
            }
        }

        return $shop_template;
    }

    /**
     * Add the provider to the shop template.
     *
     * @since 0.7
     * @param Shop_Template $shop_template
     * @return Shop_Template
     */
    protected function add_provider(Shop_Template $shop_template)
    {
        $provider_name = carbon_get_post_meta($shop_template->get_id()->get_value(), self::PROVIDER);
        if(!empty($provider_name)) {
            $provider = $this->provider_repository->find_by_name(new Name($provider_name));
            if($provider !== null) {
                $shop_template->set_provider($provider);
            }
        }

        return $shop_template;
    }

    /**
     * Store the thumbnail into the shop template.
     *
     * @since 0.6
     * @param Shop_template $shop_template
     */
    protected function store_thumbnail(Shop_Template $shop_template)
    {
        if ($shop_template->has_thumbnail() && !wp_is_post_revision($shop_template->get_id()->get_value())) {
            $thumbnail_id = $shop_template->get_thumbnail()->get_id()->get_value();
            $this->store_post_meta($shop_template->get_id(), self::THUMBNAIL_ID, $thumbnail_id);
        }
    }

    /**
     * Store the provider into the shop template.
     *
     * @since 0.7
     * @param Shop_Template $shop_template
     */
    protected function store_provider(Shop_Template $shop_template)
    {
        if(!$shop_template->has_provider()) {
            return;
        }

        $name = $shop_template->get_provider()->get_name();
        $this->store_post_meta($shop_template->get_id(), self::PROVIDER, $name);
    }

    /**
     * Build the default args from the saved shop template in the database.
     *
     * @since 0.6
     * @param Shop_template $shop_template
     *
     * @return array
     */
    protected function get_default_args(Shop_Template $shop_template)
    {
        $default_args = array();
        if($shop_template->has_id()) {
            $default_args = get_post($shop_template->get_id()->get_value(), ARRAY_A);
        }

        return $default_args;
    }

    /**
     * Build the args to save the shop template.
     *
     * @since 0.6
     * @param Shop_template $shop_template
     *
     * @param array $default_args
     * @return array
     */
    protected function get_args(Shop_Template $shop_template, array $default_args = array())
    {
        $args = wp_parse_args(array(
            'post_title' => $shop_template->get_title()->get_value(),
            'post_status' => 'publish',
            'post_name' => $shop_template->get_name()->get_value(),
            'post_type' => Shop_Template::POST_TYPE,
        ), $default_args);

        if($shop_template->has_id()) {
            $args['id'] = $shop_template->get_id()->get_value();
        }

        return $args;
    }
}
