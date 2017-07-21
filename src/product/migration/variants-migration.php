<?php
namespace Affilicious\Product\Migration;

use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Attribute\Model\Value;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Affilicious\Product\Factory\Product_Variant_Factory_Interface;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Tag;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;
use Affilicious\Shop\Model\Pricing;
use Affilicious\Shop\Model\Tracking;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Variants_Migration
{
    /**
     * @var Product_Repository_Interface
     */
    private $product_repository;

    /**
     * @var Attribute_Template_Repository_Interface
     */
    private $attribute_template_repository;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @var Product_Variant_Factory_Interface
     */
    private $product_variant_factory;

    /**
     * @since 0.8
     * @param Product_Repository_Interface $product_repository
     * @param Attribute_Template_Repository_Interface $attribute_template_repository
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Product_Variant_Factory_Interface $product_variant_factory
     */
    public function __construct(
        Product_Repository_Interface $product_repository,
        Attribute_Template_Repository_Interface $attribute_template_repository,
        Shop_Template_Repository_Interface $shop_template_repository,
        Product_Variant_Factory_Interface $product_variant_factory
    ) {
        $this->product_repository = $product_repository;
        $this->attribute_template_repository = $attribute_template_repository;
        $this->shop_template_repository = $shop_template_repository;
        $this->product_variant_factory = $product_variant_factory;
    }

    /**
     * Migrate the old variants to the new products
     *
     * @since 0.8
     */
    public function migrate()
    {
        global $wpdb;

        $products = $this->product_repository->find_all();
        foreach ($products as $product) {
            if(!($product instanceof Complex_Product)) {
                continue;
            }

            $attribute_group_key = carbon_get_post_meta($product->get_id()->get_value(), '_affilicious_product_attribute_group_key');
            if(empty($attribute_group_key)) {
                continue;
            }

            $fields = carbon_get_post_meta($product->get_id()->get_value(), '_affilicious_product_variants', 'complex');
            if(!empty($fields)) {
                foreach ($fields as $field) {
                    if(!isset($field['_type']) || $field['_type'] !== ('_' . $attribute_group_key)) {
                        continue;
                    }

                    if(empty($field['variant_id']) || empty($field['title'])) {
                        continue;
                    }

                    $post = get_post($field['variant_id']);
                    if(empty($post)) {
                        continue;
                    }

                    $name = new Name($field['title']);
                    $slug = new Slug($post->post_name);
                    $variant = $this->product_variant_factory->create($product, $name, $slug);
                    $variant->set_id(new Product_Id($field['variant_id']));

                    if(isset($field['default']) && $field['default'] == 'yes') {
                        $variant->set_default(true);
                    }

                    $keys = array_keys($field);
                    foreach ($keys as $key) {
                        if(strpos($key, 'attribute_') === 0) {
                            $slug  = str_replace('attribute_', '', $key);
                            $slug = str_replace('_', '-', $slug);

                            $attribute_template = $this->attribute_template_repository->find_one_by_slug(new Slug($slug));
                            if($attribute_template === null) {
                                continue;
                            }

                            $value = new Value($field[$key]);
                            $attribute = $attribute_template->build($value);

                            $variant->add_attribute($attribute);
                        }
                    }

                    if(!empty($field['tags'])) {
                        $tags = explode(';', $field['tags']);
                        $tags = array_map(function($tag) {
                            return new Tag($tag);
                        }, $tags);

                        $variant->set_tags($tags);
                    }

                    if(!empty($field['thumbnail'])) {
                        $thumbnail_id = $field['thumbnail'];
                        $variant->set_thumbnail_id(new Image_Id($thumbnail_id));
                    }

                    if(!empty($field['shops'])) {
                        $shops = $field['shops'];
                        foreach ($shops as $shop) {
                            if(!isset($shop['_type'])) {
                                continue;
                            }

                            $slug = str_replace('_', '-', substr($shop['_type'], 1, strlen($shop['_type'])));
                            $affiliate_link = !empty($shop['affiliate_link']) ? $shop['affiliate_link'] : null;
                            $affiliate_product_id = !empty($shop['affiliate_product_id']) ? $shop['affiliate_product_id'] : null;
                            $availability = !empty($shop['availability']) ? $shop['availability'] : null;
                            $currency = !empty($shop['currency']) ? $shop['currency'] : null;
                            $price = !empty($shop['price']) ? $shop['price'] : null;
                            $old_price = !empty($shop['old_price']) ? $shop['old_price'] : null;
                            $updated_at = !empty($shop['updated_at']) ? $shop['updated_at'] : null;

                            if(empty($affiliate_link) || empty($availability)) {
                                continue;
                            }

                            $shop_template = $this->shop_template_repository->find_one_by_slug(new Slug($slug));
                            if($shop_template === null) {
                                continue;
                            }

                            $shop = $shop_template->build(
                                new Tracking(
                                    new Affiliate_Link($affiliate_link),
                                    $affiliate_product_id !== null ? new Affiliate_Product_Id($affiliate_product_id) : null
                                ),
                                new Pricing(
                                    new Availability($availability),
                                    $price !== null && $currency !== null ? new Money($price, new Currency($currency)) : null,
                                    $old_price !== null && $currency !== null ? new Money($old_price, new Currency($currency)) : null
                                )
                            );

                            if($updated_at !== null) {
                                $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp($updated_at));
                            }

                            $variant->add_shop($shop);
                        }
                    }

                    $product->add_variant($variant);
                }
            }

            $id = $product->get_id()->get_value();
            $wpdb->query("
                    DELETE postmeta
                    FROM $wpdb->postmeta postmeta
                    WHERE postmeta.meta_key LIKE '_affilicious_product_variants%'
                    AND postmeta.post_id = $id
                ");

            delete_post_meta($id, '_affilicious_product_attribute_group_key');

            try {
                $this->product_repository->store($product);
            } catch (\Exception $e) {
            }
        }
    }
}
