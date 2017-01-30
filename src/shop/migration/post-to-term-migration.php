<?php
namespace Affilicious\Shop\Migration;

use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Post_To_Term_Migration
{
    /**
     * @var Shop_Template_Factory_Interface
     */
    private $shop_template_factory;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @since 0.8
     * @param Shop_Template_Factory_Interface $shop_template_factory
     * @param Shop_Template_Repository_Interface $shop_template_repository
     */
    public function __construct(
        Shop_Template_Factory_Interface $shop_template_factory,
        Shop_Template_Repository_Interface $shop_template_repository
    )
    {
        $this->shop_template_factory = $shop_template_factory;
        $this->shop_template_repository = $shop_template_repository;
    }

    /**
     * Migrate the shop templates posts to taxonomy terms.
     *
     * @since 0.8
     */
    public function migrate()
    {
        $posts = get_posts(array(
            'post_type' => 'aff_shop_template',
            'status' => 'publish'
        ));

        foreach ($posts as $post) {
            $shop_template = $this->shop_template_factory->create(
                new Name($post->post_title),
                new Slug($post->post_name)
            );

            $provider_id = carbon_get_post_meta($post->ID, '_affilicious_shop_template_provider');
            if(!empty($provider_id)) {
                $shop_template->set_provider_id(new Provider_Id($provider_id));
            }

            $thumbnail_id = carbon_get_post_meta($post->ID, '_thumbnail_id');
            if(!empty($thumbnail_id)) {
                $shop_template->set_thumbnail_id(new Image_Id($thumbnail_id));
            }

            try {
                $this->shop_template_repository->store($shop_template);
            } catch (\Exception $e) {
            } finally {
                wp_delete_post($post->ID);
            }
        }
    }
}
