<?php
namespace Affilicious\Product\Helper;

use Affilicious\Attribute\Helper\Attribute_Helper;
use Affilicious\Attribute\Model\Attribute;
use Affilicious\Common\Helper\Image_Helper;
use Affilicious\Common\Model\Image;
use Affilicious\Detail\Helper\Detail_Helper;
use Affilicious\Detail\Model\Detail;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Content_Aware_Interface;
use Affilicious\Product\Model\Detail_Aware_Interface;
use Affilicious\Product\Model\Excerpt_Aware_Interface;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Relation_Aware_Interface;
use Affilicious\Product\Model\Review_Aware_Interface;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Model\Tag;
use Affilicious\Product\Model\Tag_Aware_Interface;
use Affilicious\Shop\Helper\Shop_Helper;
use Affilicious\Shop\Model\Shop;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Helper
{
    /**
     * Check if the ID or Wordpress post belongs to a product.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.7.1
     * @param int|string|array|\WP_Post|Product|Product_Id|null $post_or_id
     * @return bool
     */
    public static function is_product($post_or_id = null)
    {
        // The argument is already a product
        if ($post_or_id instanceof Product) {
            return true;
        }

        // The argument is a product ID
        if($post_or_id instanceof Product_Id) {
            return get_post_type($post_or_id->get_id()) === Product::POST_TYPE;
        }

        // The argument is an integer or string.
        if(is_int($post_or_id) || is_string($post_or_id)) {
            return get_post_type(intval($post_or_id)) === Product::POST_TYPE;
        }

        // The argument is an array
        if(is_array($post_or_id) && !empty($post_or_id['id'])) {
            return get_post(intval($post_or_id['id']));
        }

        // The argument is a post.
        if($post_or_id instanceof \WP_Post) {
            return $post_or_id->post_type === Product::POST_TYPE;
        }

        // The argument is empty. Use the current post.
        if($post_or_id === null) {
            return get_post_type() === Product::POST_TYPE;
        }

        return false;
    }

    /**
     * Get the product by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|string|array|\WP_Post|Product|Product_Id|null $post_or_id
     * @return null|Product
     */
    public static function get_product($post_or_id = null)
    {
        $product_repository = \Affilicious::get('affilicious.product.repository.product');

        // The argument is already a product.
        if ($post_or_id instanceof Product) {
            return $post_or_id;
        }

        // The argument is a product ID
        if($post_or_id instanceof Product_Id) {
            return $product_repository->find_one_by_id($post_or_id);
        }

        // The argument is an integer or string.
        if(is_int($post_or_id) || is_string($post_or_id)) {
            return $product_repository->find_one_by_id(new Product_Id($post_or_id));
        }

        // The argument is an array,
        if(is_array($post_or_id) && !empty($post_or_id['id'])) {
            return $product_repository->find_one_by_id(new Product_Id($post_or_id['id']));
        }

        // The argument is a post.
        if($post_or_id instanceof \WP_Post) {
            return $product_repository->find_one_by_id(new Product_Id($post_or_id->ID));
        }

        // The argument is null. Use the current post.
        if($post_or_id === null) {
            $post = get_post($post_or_id);
            return $post !== null ? $product_repository->find_one_by_id(new Product_Id($post->ID)) : null;
        }

        return null;
    }

    /**
     * Convert the product into an array.
     *
     * @since 0.9
     * @param Product $product
     * @return array
     */
    public static function to_array(Product $product)
    {
        $result = [
            'id' => $product->has_id() ? $product->get_id()->get_value() : null,
            'name' => $product->get_name()->get_value(),
            'slug' => $product->get_slug()->get_value(),
	        'type' => $product->get_type()->get_value(),
            'thumbnail_id' => $product->has_thumbnail_id() ? $product->get_thumbnail_id()->get_value() : null,
            'thumbnail' => $product->has_thumbnail() ? Image_Helper::to_array($product->get_thumbnail()) : null,
	        'custom_values' => $product->has_custom_values() ? $product->get_custom_values() : null,
        ];

        if($product instanceof Excerpt_Aware_Interface) {
            $result['excerpt'] = $product->has_excerpt() ? $product->get_excerpt()->get_value() : null;
        }

        if($product instanceof Content_Aware_Interface) {
            $result['content'] = $product->has_content() ? $product->get_content()->get_value() : null;
        }

        $result['image_gallery'] = !$product->has_image_gallery() ? null : array_map(function(Image $image) {
            return $image->get_id();
        }, $product->get_image_gallery());

        if($product instanceof Detail_Aware_Interface) {
            $result['details'] = !$product->has_details() ? null : array_map(function(Detail $detail) {
                return Detail_Helper::to_array($detail);
            }, $product->get_details());
        }

        if($product instanceof Shop_Aware_Interface) {
            $result['shops'] = !$product->has_shops() ? null : array_map(function(Shop $shop) {
                return Shop_Helper::to_array($shop);
            }, $product->get_shops());
        }

        if($product instanceof Review_Aware_Interface) {
            $result['review'] = $product->has_review() ? Review_Helper::to_array($product->get_review()) : null;
        }

        if($product instanceof Tag_Aware_Interface) {
            $result['tags'] = !$product->has_tags() ? null :  array_map(function(Tag $tag) {
                return $tag->get_value();
            }, $product->get_tags());
        }

        if($product instanceof Relation_Aware_Interface) {
            $result['related_products'] = !$product->has_related_products() ? null : array_map(function(Product_Id $product_id) {
                return $product_id->get_value();
            }, $product->get_related_products());
            $result['related_accessories'] = !$product->has_related_accessories() ? null : array_map(function(Product_Id $product_id) {
                return $product_id->get_value();
            }, $product->get_related_accessories());
        }

        if($product instanceof Complex_Product) {
            $result['variants'] = !$product->has_variants() ? null : array_map(function(Product_Variant $variant) {
                return self::to_array($variant);
            }, $product->get_variants());
        }

        if($product instanceof Product_Variant) {
            $result['parent'] = $product->get_parent()->has_id() ? $product->get_parent()->get_id()->get_value() : null;
            $result['attributes'] = !$product->has_attributes() ? null : array_map(function(Attribute $attribute) {
                return Attribute_Helper::to_array($attribute);
            }, $product->get_attributes());
        }

        $result = apply_filters('aff_product_to_array', $result, $product);

        return $result;
    }
}
