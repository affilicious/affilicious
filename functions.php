<?php
use Affilicious\Attribute\Helper\Attribute_Helper;
use Affilicious\Attribute\Helper\Attribute_Template_Helper;
use Affilicious\Attribute\Model\Attribute;
use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Attribute_Template_Id;
use Affilicious\Common\Admin\License\License_Status;
use Affilicious\Common\Helper\Image_Helper;
use Affilicious\Common\Helper\Time_Helper;
use Affilicious\Common\Model\Image;
use Affilicious\Common\Model\Name;
use Affilicious\Detail\Helper\Detail_Helper;
use Affilicious\Detail\Helper\Detail_Template_Helper;
use Affilicious\Detail\Model\Detail;
use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Detail_Template_Id;
use Affilicious\Product\Helper\Product_Helper;
use Affilicious\Product\Helper\Review_Helper;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Detail_Aware_Interface;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Rating;
use Affilicious\Product\Model\Relation_Aware_Interface;
use Affilicious\Product\Model\Review;
use Affilicious\Product\Model\Review_Aware_Interface;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Model\Tag;
use Affilicious\Product\Model\Tag_Aware_Interface;
use Affilicious\Product\Model\Type;
use Affilicious\Product\Model\Votes;
use Affilicious\Provider\Helper\Provider_Helper;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Shop\Helper\Money_Helper;
use Affilicious\Shop\Helper\Shop_Helper;
use Affilicious\Shop\Helper\Shop_Template_Helper;
use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Money;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Model\Shop_Template_Id;
use Affilicious\Common\Helper\Template_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * Check if the product with the Wordpress ID or post is existing.
 *
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $post_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_is_product($post_or_id = null)
{
    $result = Product_Helper::is_product($post_or_id);

    return $result;
}

/**
 * Get the product by the Wordpress ID or post.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $post_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Product The product in the given output format.
 */
function aff_get_product($post_or_id = null, $output = 'array')
{
    $product = Product_Helper::get_product($post_or_id);
    if($product === null) {
        return null;
    }

    $product = apply_filters('aff_product', $product, $post_or_id);

    if($output == 'array') {
        $product = Product_Helper::to_array($product);
    }

    $product = apply_filters('aff_formatted_product', $product, $post_or_id, $output);

    return $product;
}

/**
 * Check if the shop template with the Wordpress ID or term is existing.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Term|Shop_Template|Shop_Template_Id $term_or_id
 * @return bool
 */
function aff_is_shop_template($term_or_id)
{
    $result = Shop_Template_Helper::is_shop_template($term_or_id);

    return $result;
}

/**
 * Get the shop template by the Wordpress ID or term.
 *
 * @since 0.6
 * @param int|string|array|\WP_Term|Shop_Template|Shop_Template_Id $term_or_id
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Shop_Template The shop template in the given output format.
 */
function aff_get_shop_template($term_or_id, $output = 'array')
{
    $shop_template = Shop_Template_Helper::get_shop_template($term_or_id);
    if($shop_template === null) {
        return null;
    }

    $shop_template = apply_filters('aff_shop_template', $shop_template, $term_or_id);

    if($output == 'array') {
        $shop_template = Shop_Template_Helper::to_array($shop_template);
    }

    $shop_template = apply_filters('aff_formatted_shop_template', $shop_template, $term_or_id, $output);

    return $shop_template;
}

/**
 * Check if the detail template with the Wordpress ID or term is existing.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Term|Detail_Template|Detail_Template_Id $term_or_id
 * @return bool
 */
function aff_is_detail_template($term_or_id)
{
    $result = Detail_Template_Helper::is_detail_template($term_or_id);

    return $result;
}

/**
 * Get the detail template by the Wordpress ID or term.
 *
 * @since 0.8
 * @param int|string|array|\WP_Term|Detail_Template|Detail_Template_Id $term_or_id
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Detail_Template The detail template in the given output format.
 */
function aff_get_detail_template($term_or_id, $output = 'array')
{
    $detail_template = Detail_Template_Helper::get_detail_template($term_or_id);
    if($detail_template === null) {
        return null;
    }

    $detail_template = apply_filters('aff_detail_template', $detail_template, $term_or_id);

    if($output == 'array') {
        $detail_template = Detail_Template_Helper::to_array($detail_template);
    }

    $detail_template = apply_filters('aff_formatted_detail_template', $detail_template, $term_or_id, $output);

    return $detail_template;
}

/**
 * Check if the attribute template with the Wordpress ID or term is existing.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Term|Attribute_Template|Attribute_Template_Id $term_or_id
 * @return bool
 */
function aff_is_attribute_template($term_or_id)
{
    $result = Attribute_Template_Helper::is_attribute_template($term_or_id);

    return $result;
}

/**
 * Get the attribute template by the Wordpress ID or term.
 *
 * @since 0.8
 * @param int|string|array|\WP_Term|Attribute_Template|Attribute_Template_Id $term_or_id
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Attribute_Template The attribute in the given output format.
 */
function aff_get_attribute_template($term_or_id, $output = 'array')
{
    $attribute_template = Attribute_Template_Helper::get_attribute_template($term_or_id);
    if($attribute_template === null) {
        return null;
    }

    $attribute_template = apply_filters('aff_attribute_template', $attribute_template, $term_or_id);

    if($output == 'array') {
        $attribute_template = Attribute_Template_Helper::to_array($attribute_template);
    }

    $attribute_template = apply_filters('aff_formatted_attribute_template', $attribute_template, $term_or_id, $output);

    return $attribute_template;
}

/**
 * Check if the provider with the provider ID is existing.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Term|Provider|Provider_Id $provider_id
 * @return bool
 */
function aff_is_provider($provider_id)
{
    $result = Provider_Helper::is_provider($provider_id);

    return $result;
}

/**
 * Get the provider by the provider ID.
 *
 * @since 0.8
 * @param int|string|array|\WP_Term|Provider|Provider_Id $provider_id
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Provider The provider in the given output format.
 */
function aff_get_provider($provider_id, $output = 'array')
{
    $provider = Provider_Helper::get_provider($provider_id);
    if($provider === null) {
        return null;
    }

    $provider = apply_filters('aff_provider', $provider, $provider_id);

    if($output == 'array') {
        $provider = Provider_Helper::to_array($provider);
    }

    $provider = apply_filters('aff_formatted_provider', $provider, $provider_id, $output);

    return $provider;
}

/**
 * Check if the current page is a product.
 *
 * @since 0.6
 * @return bool
 */
function aff_is_product_page()
{
    return is_singular(Product::POST_TYPE);
}

/**
 * Get a list of all available product taxonomies.
 *
 * @since 0.8.17
 * @param string $output Optional. The type of output to return in the array. Accepts either taxonomy 'names' or 'objects'. Default 'names'.
 * @param bool $only_custom Whether the taxonomies should contains only custom ones or including the taxonomies like 'aff_shop_tmpl', 'aff_detail_tmpl' and 'aff_attribute_tmpl'. Default 'true'-
 * @return array
 */
function aff_get_product_taxonomies($output = 'names', $only_custom = true)
{
    $taxonomies = get_object_taxonomies(Product::POST_TYPE, $output);

    // Filter the default product taxonomies if only custom ones are allowed.
    if($only_custom) {
        foreach ($taxonomies as $index => $taxonomy) {
            $name = $taxonomy instanceof WP_Taxonomy ? $taxonomy->name : $taxonomy;

            if(in_array($name, [Shop_Template::TAXONOMY, Detail_Template::TAXONOMY, Attribute_Template::TAXONOMY])) {
                unset($taxonomies[$index]);
            }
        }

        // Reset the indices.
        $taxonomies = array_values($taxonomies);
    }

    $taxonomies = apply_filters('aff_product_taxonomies', $taxonomies, $output, $only_custom);

    return $taxonomies;
}

/**
 * Get the product name.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return Name|null|string The name in the given output format.
 */
function aff_get_product_name($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    $name = $product->get_name();
    $name = apply_filters('aff_product_name', $name, $product);
    if(empty($name)) {
        return null;
    }

    if($output == 'scalar') {
        $name = $name->get_value();
    }

    $name = apply_filters('aff_product_formatted_name', $name, $product, $output);

    return $name;
}

/**
 * Print the shop name.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_name($product_or_id = null, $escape = true)
{
    $name = aff_get_product_name($product_or_id, 'scalar');
    if($name == null) {
        return;
    }

    if($escape) {
        $name = esc_html($name);
    }

    echo $name;
}

/**
 * Check if the product has a review.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_review($product_or_id = null)
{
    $review = aff_get_product_review($product_or_id, 'object');
    $result = $review !== null;

    return $result;
}

/**
 * Get the product review.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Review The review in the given output format.
 */
function aff_get_product_review($product_or_id = null, $output = 'array')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product instanceof Product_Variant) {
        $product = $product->get_parent();
    }

    if(!($product instanceof Review_Aware_Interface)) {
        return null;
    }

    $review = $product->get_review();
    if($review === null) {
        return null;
    }

    $review = apply_filters('aff_product_review', $review, $product);

    if($output == 'array') {
        $review = Review_Helper::to_array($review);
    }

    $review = apply_filters('aff_product_formatted_review', $review, $product, $output);

    return $review;
}

/**
 * Check if the product has a review rating.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_review_rating($product_or_id = null)
{
    $rating = aff_get_product_review_rating($product_or_id);
    $result = !empty($rating) || $rating === 0;

    return $result;
}

/**
 * Get the product review rating from 0 to 5.
 *
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return null|float|Rating The review rating in the given output format.
 */
function aff_get_product_review_rating($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    $review = aff_get_product_review($product_or_id, 'object');
    if($review === null) {
        return null;
    }

    $rating = $review->get_rating();
    $rating = apply_filters('aff_product_review_rating', $rating, $review, $product);

    if($output == 'scalar') {
        $rating = $rating->get_value();
    }

    $rating = apply_filters('aff_product_review_formatted_rating', $rating, $review, $product, $output);

    return $rating;
}

/**
 * Print the product review rating from 0 to 5 as stars.
 *
 * @since 0.8.9
 * @param string $full_star
 * @param string $half_star
 * @param string $no_star
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 */
function aff_the_product_review_rating($full_star, $half_star, $no_star, $product_or_id = null)
{
    $rating = aff_get_product_review_rating($product_or_id);
    if(empty($rating) && $rating !== 0) {
        return;
    }

    for($i = 0; $i < 5; $i++) {
        if ($rating >= ($i + 1)) {
            echo $full_star;
        } elseif ($rating >= ($i + 0.5)) {
            echo $half_star;
        } else {
            echo $no_star;
        }
    }
}

/**
 * Check if the product has any review votes.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_review_votes($product_or_id = null)
{
    $votes = aff_get_product_review_votes($product_or_id, 'object');
    $result = $votes !== null;

    return $result;
}

/**
 * Get the product review votes.
 *
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return null|int|Votes The product votes in the given output format.
 */
function aff_get_product_review_votes($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    $review = aff_get_product_review($product, 'object');
    if($review === null) {
        return null;
    }

    $votes = $review->get_votes();
    if($votes === null) {
        return null;
    }

    $votes = apply_filters('aff_product_review_votes', $votes, $review, $product);

    if($output == 'scalar') {
        $votes = $votes->get_value();
    }

    $votes = apply_filters('aff_product_review_formatted_votes', $votes, $review, $product, $output);

    return $votes;
}

/**
 * Print the product review votes.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_review_votes($product_or_id = null, $escape = true)
{
    $votes = aff_get_product_review_votes($product_or_id, 'scalar');
    if($votes === null) {
        return;
    }

    echo sprintf(_n(
        'based on %s review',
        'based on %s reviews',
        $votes, 'affilicious'),
        $escape ? esc_html($votes) : $votes
    );
}

/**
 * Get the plain product details of the detail groups.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return array|Detail[] The details in the given output format.
 */
function aff_get_product_details($product_or_id = null, $output = 'array')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product instanceof Product_Variant) {
        $product = $product->get_parent();
    }

    if(!($product instanceof Detail_Aware_Interface)) {
        return [];
    }

    $details = $product->get_details();
    $details = apply_filters('aff_product_details', $details, $product);
    if(empty($details)) {
        return [];
    }

    if($output == 'array') {
        $details = array_map(function(Detail $detail) {
            return Detail_Helper::to_array($detail);
        }, $details);
    }

    $details = apply_filters('aff_product_formatted_details', $details, $product, $output);

    return $details;
}

/**
 * Check if the product has any details.
 *
 * @since 0.9.14
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product has details or not.
 */
function aff_has_product_details($product_or_id = null)
{
	$details = aff_get_product_details($product_or_id, 'array');
	if(empty($details)) {
		return false;
	}

	return true;
}

/**
 * Check if the product has a thumbnail.
 *
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product has a thumbnail or not.
 */
function aff_has_product_thumbnail($product_or_id = null)
{
    $thumbnail = aff_get_product_thumbnail($product_or_id, 'object');
    $result = !empty($thumbnail);

    return $result;
}

/**
 * Get the thumbnail by the product.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Choose from "scalar", "array" or "object". Default: "scalar".
 * @return null|int|array|Image The image in the given output format.
 */
function aff_get_product_thumbnail($product_or_id = null, $output = 'array')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    $thumbnail = $product->get_thumbnail();
    $thumbnail = apply_filters('aff_product_thumbnail', $thumbnail, $product);
    if(empty($thumbnail)) {
        return null;
    }

    if($output == 'scalar') {
        $thumbnail = $thumbnail->get_id();
    }

    if($output == 'array') {
        $thumbnail = Image_Helper::to_array($thumbnail);
    }

    $thumbnail = apply_filters('aff_product_formatted_thumbnail', $thumbnail, $product, $output);

    return $thumbnail;
}

/**
 * Print the product thumbnail.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|array $size Image size to use. Accepts any valid image size, or an array of width and height values in pixels (in that order). Default value: 'post-thumbnail'.
 * @param string|array $attr Query string or array of attributes. Default value: ''.
 */
function aff_the_product_thumbnail($product_or_id = null, $size = 'post-thumbnail', $attr = '')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return;
    }

    $thumbnail = get_the_post_thumbnail($product->get_id()->get_value(), $size, $attr);
    echo $thumbnail;
}

/**
 * Check if the product has an image gallery.
 *
 * @since 0.8.5
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_image_gallery($product_or_id = null)
{
    $image_gallery = aff_get_product_image_gallery($product_or_id);
    $result = !empty($image_gallery);

    return $result;
}

/**
 * Get the image gallery by the product.
 *
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Choose from "scalar", "array" or "object". Default: "scalar".
 * @return int[]|array|Image[] The image of the image gallery in the given output format.
 */
function aff_get_product_image_gallery($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return [];
    }

    $images = $product->get_image_gallery();
    if(empty($images)) {
        return [];
    }

    $images = apply_filters('aff_product_image_gallery', $images, $product);

    if($output == 'scalar') {
        $images = array_map(function(Image $image) {
            return $image->get_id();
        }, $images);
    }

    if($output == 'array') {
        $images = array_map(function(Image $image) {
            return Image_Helper::to_array($image);
        }, $images);
    }

    $images = apply_filters('aff_product_formatted_image_gallery', $images, $product, $output);

    return $images;
}

/**
 * Check if the product has any shops.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_shops($product_or_id = null)
{
    $shops = aff_get_product_shops($product_or_id);
    $result = !empty($shops);

    return $result;
}

/**
 * Get the shops by the product.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return array|Shop[] The shop in the given output format.
 */
function aff_get_product_shops($product_or_id = null, $output = 'array')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return [];
    }

    if ($product instanceof Complex_Product) {
        $product = $product->get_default_variant();
    }

    if(!($product instanceof Shop_Aware_Interface)) {
        return [];
    }

    $shops = $product->get_shops();
    if(empty($shops)) {
        return [];
    }

    $shops = apply_filters('aff_product_shops', $shops, $product);

    if($output == 'array') {
        $shops = array_map(function(Shop $shop) {
            return Shop_Helper::to_array($shop);
        }, $shops);
    }

    $shops = apply_filters('aff_product_formatted_shops', $shops, $product, $output);

    return $shops;
}

/**
 * Check if the product has any related products.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_related_products($product_or_id = null)
{
    $product_ids = aff_get_product_related_products($product_or_id);
    $result = !empty($product_ids);

    return $result;
}

/**
 * Get the related products by the product.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return int[]|Product_Id[] The IDs of the related products in the given output format.
 */
function aff_get_product_related_products($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if(!($product instanceof Relation_Aware_Interface)) {
        return [];
    }

    $related_products = $product->get_related_products();
    if(empty($related_products)) {
        return [];
    }

    $related_products = apply_filters('aff_product_related_products', $related_products, $product);

    if($output == 'scalar') {
        $related_products = array_map(function(Product_Id $related_product) {
            return $related_product->get_value();
        }, $related_products);
    }

    $related_products = apply_filters('aff_product_formatted_related_products', $related_products, $product, $output);

    return $related_products;
}

/**
 * Get the query for the products.
 *
 * @since 0.7.1
 * @param array $args
 * @return WP_Query
 */
function aff_get_products_query($args = array())
{
    // It's not allowed to set a custom post type.
    unset($args['post_type']);

    $options = wp_parse_args($args, array(
        'post_type' => Product::POST_TYPE,
        'order_by' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the query of the related products by the product.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param array $args
 * @return null|WP_Query
 */
function aff_get_product_related_products_query($product_or_id = null, $args = array())
{
    $related_product_ids = aff_get_product_related_products($product_or_id, 'scalar');
    if (empty($related_product_ids)) {
        return null;
    }

    // It's not allowed to set a custom post type or use custom IDs.
    unset($args['post_type'], $args['post__in']);

    $options = wp_parse_args($args, array(
        'post_type' => Product::POST_TYPE,
        'post__in' => $related_product_ids,
        'orderby' => 'post__in',
        'order' => 'ASC'
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Check if the product has any related accessories.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_related_accessories($product_or_id = null)
{
    $accessories = aff_get_product_related_accessories($product_or_id, 'objects');
    $result = !empty($accessories);

    return $result;
}

/**
 * Get the related accessories by the product.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return int[]|Product_Id[] The IDs of the related accessories in the given output format.
 */
function aff_get_product_related_accessories($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null || !($product instanceof Relation_Aware_Interface)) {
        return [];
    }

    $related_accessories = $product->get_related_accessories();
    if(empty($related_accessories)) {
        return null;
    }

    $related_accessories = apply_filters('aff_product_related_accessories', $related_accessories, $product);

    if($output == 'scalar') {
        $related_accessories = array_map(function(Product_Id $related_accessory) {
            return $related_accessory->get_value();
        }, $related_accessories);
    }

    $related_accessories = apply_filters('aff_product_formatted_related_accessories', $related_accessories, $product, $output);

    return $related_accessories;
}

/**
 * Get the query of the related accessories by the product.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param array $args
 * @return null|WP_Query
 */
function aff_get_product_related_accessories_query($product_or_id = null, $args = array())
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    $related_accessories_ids = aff_get_product_related_accessories($product, 'scalar');
    if (empty($related_accessories_ids)) {
        return null;
    }

    // It's not allowed to set a custom post type or use custom IDs.
    unset($args['post_type'], $args['post__in']);

    $options = wp_parse_args($args, array(
        'post_type' => Product::POST_TYPE,
        'post__in' => $related_accessories_ids,
        'orderby' => 'post__in',
        'order' => 'ASC'
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the product link.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return null|string The product link.
 */
function aff_get_product_link($product_or_id = null)
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    $link = get_permalink($product->get_post());
    if(empty($link)) {
        return null;
    }

    $link = apply_filters('aff_product_link', $link, $product);

    return $link;
}

/**
 * Print the product link.
 *
 * @since 0.8.8
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_link($product_or_id = null, $escape = true)
{
    $link = aff_get_product_link($product_or_id);

    if($escape) {
        $link = esc_url($link);
    }

    echo $link;
}

/**
 * Check if the given product has any tags.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool
 */
function aff_has_product_tags($product_or_id = null)
{
    $tags = aff_get_product_tags($product_or_id, 'object');
    $result = !empty($tags);

    return $result;
}

/**
 * Get the tags of the given product.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return string[]|Tag[] The tags in the given output format.
 */
function aff_get_product_tags($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return [];
    }

    if($product instanceof Complex_Product) {
        $product = $product->get_default_variant();
    }

    if(!($product instanceof Tag_Aware_Interface)) {
        return [];
    }

    $tags = $product->get_tags();
    if(empty($tags)) {
        return [];
    }

    $tags = apply_filters('aff_product_tags', $tags, $product);

    if($output == 'scalar') {
        $tags = array_map(function(Tag $tag) {
            return $tag->get_value();
        }, $tags);
    }

    $tags = apply_filters('aff_product_formatted_tags', $tags, $product, $output);

    return $tags;
}

/**
 * Print the tags of the given product.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $before The html or string which is printed before every tag.
 * @param string $after The html or string which is printed after every tag.
 * @param bool $escape Whether to escape the output.
 */
function aff_the_product_tags($product_or_id = null, $before = '', $after = '', $escape = true)
{
    $tags = aff_get_product_tags($product_or_id, 'scalar');
    if(empty($tags)) {
        return;
    }

    foreach ($tags as $tag) {
        if($escape) {
            $tag = esc_html($tag);
        }

        echo $before . $tag . $after;
    }
}

/**
 * Get the shop of the given product.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|Affiliate_Link|null $affiliate_link If you pass in nothing as an affiliate link, the cheapest shop will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Shop The shop in the given output format.
 */
function aff_get_product_shop($product_or_id = null, $affiliate_link = null, $output = 'array')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product instanceof Complex_Product) {
        $product = $product->get_default_variant();
    }

    if(!($product instanceof Shop_Aware_Interface)) {
        return null;
    }

    $shop = null;
    if($affiliate_link instanceof Affiliate_Link) {
        $shop = $product->get_shop($affiliate_link);
    } elseif ($affiliate_link === null) {
        $shop = $product->get_cheapest_shop();
    } elseif (is_string($affiliate_link)) {
        $shop = $product->get_shop(new Affiliate_Link($affiliate_link));
    }

    if($shop === null) {
        return null;
    }

    $cheapest = $affiliate_link === null;
    $shop = apply_filters('aff_product_shop', $shop, $product, $cheapest);

    if($output == 'array') {
        $shop = Shop_Helper::to_array($shop);
    }

    $shop = apply_filters('aff_product_formatted_shop', $shop, $product, $cheapest, $output);

    return $shop;
}

/**
 * Get the cheapest shop of the given product.
 *
 * @deprecated 1.1 Use 'aff_get_product_shop' instead.
 * @since 0.5.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Shop The shop in the given output format.
 */
function aff_get_product_cheapest_shop($product_or_id = null, $output = 'array')
{
    return aff_get_product_shop($product_or_id, null, $output);
}

/**
 * Check if the product has any discounted price.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|Affiliate_Link|null $affiliate_link If you pass in nothing as an affiliate link, the cheapest shop will be used.
 * @return bool Whether the product has a price or not.
 */
function aff_has_product_price($product_or_id = null, $affiliate_link = null)
{
    $price = aff_get_product_price($product_or_id, $affiliate_link, 'object');
    $result = $price !== null;

    return $result;
}

/**
 * Get the discounted price with the currency of the product.
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|Affiliate_Link|null $affiliate_link If you pass in nothing as an affiliate link, the cheapest shop will be used.
 * @param string $output The required return type. One of "scalar", "array" or "object". Default: "scalar".
 * @return null|string|Money The price in the given output format.
 */
function aff_get_product_price($product_or_id = null, $affiliate_link = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    if($product instanceof Complex_Product) {
        $product = $product->get_default_variant();
    }

    if(!($product instanceof Shop_Aware_Interface)) {
        return null;
    }

    $shop = aff_get_product_shop($product, $affiliate_link, 'object');
    if($shop === null) {
        return $shop;
    }

    $price = $shop->get_pricing()->get_price();
    if($price === null) {
        return null;
    }

    $price = apply_filters('aff_product_price', $price, $product, $shop);

    if($output == 'scalar') {
        $price = Money_Helper::to_string($price);
    } elseif ($output == 'array') {
        $price = Money_Helper::to_array($price);
    }

    $price = apply_filters('aff_product_formatted_price', $price, $product, $shop, $output);

    return $price;
}

/**
 * Print the price with the currency of the product.
 *
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|Affiliate_Link|null $affiliate_link If you pass in nothing as an affiliate link, the cheapest shop will be used.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_price($product_or_id = null, $affiliate_link = null, $escape = true)
{
    $price = aff_get_product_price($product_or_id, $affiliate_link, 'scalar');
    if(empty($price)) {
        return;
    };

    if($escape) {
        $price = esc_html($price);
    }

    echo $price;
}

/**
 * Check if the product has any old price.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|Affiliate_Link|null $affiliate_link If you pass in nothing as an affiliate link, the cheapest shop will be used.
 * @return bool Whether the product has an old price or not.
 */
function aff_has_product_old_price($product_or_id = null, $affiliate_link = null)
{
    $price = aff_get_product_old_price($product_or_id, $affiliate_link, 'object');
    $result = $price !== null;

    return $result;
}

/**
 * Get the old price with the currency of the product.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|Affiliate_Link|null $affiliate_link If you pass in nothing as an affiliate link, the cheapest shop will be used.
 * @param string $output The required return type. One of "scalar", "array" or "object". Default: "scalar".
 * @return null|string|Money The old price in the given output format.
 */
function aff_get_product_old_price($product_or_id = null, $affiliate_link = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    if($product instanceof Complex_Product) {
        $product = $product->get_default_variant();
    }

    if(!($product instanceof Shop_Aware_Interface)) {
        return null;
    }

    $shop = aff_get_product_shop($product, $affiliate_link, 'object');
    if($shop === null) {
        return $shop;
    }

    $old_price = $shop->get_pricing()->get_old_price();
    if($old_price === null) {
        return null;
    }

    $old_price = apply_filters('aff_product_old_price', $old_price, $product, $shop);

    if($output == 'scalar') {
        $old_price = Money_Helper::to_string($old_price);
    } elseif ($output == 'array') {
        $old_price = Money_Helper::to_array($old_price);
    }

    $old_price = apply_filters('aff_product_formatted_old_price', $old_price, $product, $shop, $output);

    return $old_price;
}

/**
 * Print the old price with the currency of the product.
 *
 * @since 0.8.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string|Affiliate_Link|null $affiliate_link If you pass in nothing as an affiliate link, the cheapest shop will be used.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_old_price($product_or_id = null, $affiliate_link = null, $escape = true)
{
    $old_price = aff_get_product_old_price($product_or_id, $affiliate_link, 'scalar');
    if($old_price === null) {
        return;
    };

    if($escape) {
        $old_price = esc_html($old_price);
    }

    echo $old_price;
}

/**
 * Get the cheapest price with the currency of the product.
 *
 * @deprecated 1.1 Use 'aff_get_product_price' instead.
 * @since 0.5.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. One of "scalar", "array" or "object". Default: "scalar".
 * @return null|string|Money The cheapest price in the given output format.
 */
function aff_get_product_cheapest_price($product_or_id = null, $output = 'scalar')
{
    return aff_get_product_price($product_or_id, null, $output);
}

/**
 * Get the cheapest old price with the currency of the product.
 *
 * @deprecated 1.1 Use 'aff_get_product_old_price' instead.
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. One of "scalar", "array" or "object". Default: "scalar".
 * @return null|string|Money The cheapest old price in the given output format.
 */
function aff_get_product_cheapest_old_price($product_or_id = null, $output = 'scalar')
{
    return aff_get_product_old_price($product_or_id, null, $output);
}

/**
 * Get the affiliate link by the product and shop
 *
 * @since 0.3
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param int|string|array|\WP_Term|Shop_Template|Shop_Template_Id|null $shop_or_id If you pass in nothing as a shop, the cheapest shop will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return null|string|Affiliate_Link The product affiliate link in the given output format.
 */
function aff_get_product_affiliate_link($product_or_id = null, $shop_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product instanceof Complex_Product) {
        $product = $product->get_default_variant();
    }

    if(!($product instanceof Shop_Aware_Interface)) {
        return null;
    }

    $shop = aff_get_product_shop($product, $shop_or_id, 'object');
    if(empty($shop)) {
        return null;
    }

    $affiliate_link = $shop->get_tracking()->get_affiliate_link();
    $affiliate_link = apply_filters('aff_product_affiliate_link', $affiliate_link, $product, $shop);

    if($output == 'scalar') {
        $affiliate_link = $affiliate_link->get_value();
    }

    $affiliate_link = apply_filters('aff_product_formatted_affiliate_link', $affiliate_link, $product, $shop, $output);

    return $affiliate_link;
}

/**
 * Print the affiliate link by the product and shop.
 *
 * @since 0.8.8
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param int|string|array|\WP_Term|Shop_Template|Shop_Template_Id|null $shop_or_id If you pass in nothing as a shop, the cheapest shop will be used.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_affiliate_link($product_or_id = null, $shop_or_id = null, $escape = true)
{
    $affiliate_link = aff_get_product_affiliate_link($product_or_id, $shop_or_id, 'scalar');
    if($affiliate_link === null) {
        return;
    }

    if($escape) {
        $affiliate_link = esc_url($affiliate_link);
    }

    echo $affiliate_link;
}

/**
 * Get the affiliate link by the product and shop.
 *
 * @deprecated 1.1 Use 'aff_the_product_affiliate_link' instead.
 * @since 0.5.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return null|string|Affiliate_Link The cheapest affiliate link in the given output format.
 */
function aff_get_product_cheapest_affiliate_link($product_or_id = null, $output = 'scalar')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    $shop = aff_get_product_cheapest_shop($product, 'object');
    if($shop === null) {
        return null;
    }

    $affiliate_link = aff_get_product_affiliate_link($product, 'object');
    if($affiliate_link === null) {
        return null;
    }

    if($output == 'scalar') {
        $affiliate_link = $affiliate_link->get_value();
    }

    return $affiliate_link;
}

/**
 * Check if the product is of the given type.
 *
 * @deprecated 1.1 Use 'aff_is_product_type' instead.
 * @since 0.6
 * @param string|Type $type
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of the type or not.
 */
function aff_product_is_type($type, $product_or_id = null)
{
    return aff_is_product_type($type, $product_or_id);
}

/**
 * Check if the product is of the given type.
 *
 * @since 0.9
 * @param string|Type $type
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of the type or not.
 */
function aff_is_product_type($type, $product_or_id = null)
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return false;
    }

    if(!($type instanceof Type)) {
        $type = new Type($type);
    }

    $result = $product->get_type()->is_equal_to($type);
    $result = apply_filters('aff_is_product_type', $result, $product, $type);

    return $result;
}

/**
 * Check if the product is a simple product.
 *
 * @deprecated 1.1 Use 'aff_is_product_simple' instead.
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of simple type or not.
 */
function aff_product_is_simple($product_or_id = null)
{
    return aff_is_product_simple($product_or_id);
}

/**
 * Check if the product is a simple product.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of simple type or not.
 */
function aff_is_product_simple($product_or_id = null)
{
    $result = aff_is_product_type(Type::simple(), $product_or_id);

    return $result;
}

/**
 * Check if the product is a complex product.
 *
 * @deprecated 1.1 Use 'aff_is_product_complex' instead.
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of complex type or not.
 */
function aff_product_is_complex($product_or_id = null)
{
    return aff_is_product_complex($product_or_id);
}

/**
 * Check if the product is a complex product.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of complex type or not.
 */
function aff_is_product_complex($product_or_id = null)
{
    $result = aff_is_product_type(Type::complex(), $product_or_id);

    return $result;
}

/**
 * Check if the product is a product variant.
 *
 * @deprecated 1.1 Use 'aff_is_product_variant' instead
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of variant type or not.
 */
function aff_product_is_variant($product_or_id = null)
{
    return aff_is_product_variant($product_or_id);
}

/**
 * Check if the product is a product variant.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product is of variant type or not.
 */
function aff_is_product_variant($product_or_id = null)
{
    $result = aff_is_product_type(Type::variant(), $product_or_id);

    return $result;
}

/**
 * Get the parent of the product variant.
 * If the given product is already the parent, it will be returned instead.
 *
 * @deprecated 1.1 Use 'aff_get_product_variant_parent' instead
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return null|Product The complex parent product of the given product variant.
 */
function aff_product_get_parent($product_or_id = null)
{
    return aff_get_product_variant_parent($product_or_id, 'object');
}

/**
 * Get the parent of the product variant.
 * If the given product is already the parent, it will be returned instead.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Product The complex parent product of the given product variant.
 */
function aff_get_product_variant_parent($product_or_id = null, $output = 'array')
{
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return null;
    }

    if($product instanceof Product_Variant) {
        $product = $product->get_parent();
    }

    if(!($product instanceof Complex_Product)) {
        return null;
    }

    if($output == 'array') {
        $product = Product_Helper::to_array($product);
    }

    return $product;
}

/**
 * Check if the given parent complex product contains the variants
 *
 * @deprecated 1.1 Use 'aff_has_product_variant' instead
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @return bool Whether the complex parent product has the variant or not.
 */
function aff_product_has_variant($complex_or_id = null, $variant_or_id = null)
{
    return aff_has_product_variant($complex_or_id, $variant_or_id);
}

/**
 * Check if the given parent complex product contains the variants
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @return bool Whether the complex parent product has the variant or not.
 */
function aff_has_product_variant($complex_or_id = null, $variant_or_id = null)
{
    $product_variant = aff_get_product_variant($complex_or_id, $variant_or_id, 'object');
    $result = $product_variant !== null;

    return $result;
}

/**
 * Get the product variant by the complex parent product.
 *
 * @deprecated 1.1 Use 'aff_get_product_variant' instead
 * @since 0.8
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @return null|Product_Variant The product variant as an object.
 */
function aff_product_get_variant($complex_or_id = null, $variant_or_id = null)
{
    return aff_get_product_variant($complex_or_id, $variant_or_id, 'object');
}

/**
 * Get the product variant by the complex parent product.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Product_Variant The product variant in the given output format.
 */
function aff_get_product_variant($complex_or_id = null, $variant_or_id = null, $output = 'array')
{
    $complex_product = aff_get_product($complex_or_id, 'object');
    if(!($complex_product instanceof Complex_Product)) {
        return null;
    }

    $product_variant = aff_get_product($variant_or_id, 'object');
    if(!($product_variant instanceof Product_Variant)) {
        return null;
    }

    $product_variant = $complex_product->get_variant($product_variant->get_slug());
    if($product_variant === null) {
        return null;
    }

    $product_variant = apply_filters('aff_product_variant', $product_variant, $complex_product);

    if($output == 'array') {
        $product_variant = Product_Helper::to_array($product_variant);
    }

    $product_variant = apply_filters('aff_product_formatted_variant', $product_variant, $complex_product, $output);

    return $product_variant;
}

/**
 * Check if the given product has any variants.
 *
 * @deprecated 1.1 Use 'aff_has_product_variants' instead
 * @since 0.7.1
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id  If you pass in nothing as a complex product, the current post will be used.
 * @return bool Whether the complex parent product has some variants or not.
 */
function aff_product_has_variants($complex_or_id = null)
{
    return aff_has_product_variants($complex_or_id);
}

/**
 * Check if the given product has any variants.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @return bool Whether the complex parent product has some variants or not.
 */
function aff_has_product_variants($complex_or_id = null)
{
    $product_variants = aff_get_product_variants($complex_or_id, 'object');
    $result = !empty($product_variants);

    return $result;
}

/**
 * Get the product variants of the given product.
 *
 * @deprecated 1.1 Use 'aff_get_product_variants' instead.
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @return Product_Variant[] All product variants of the given complex parent product.
 */
function aff_product_get_variants($complex_or_id = null)
{
    return aff_get_product_variants($complex_or_id, 'object');
}

/**
 * Get the product variants of the given product.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return array|Product_Variant[] All product variants of the given complex parent product in the given output format.
 */
function aff_get_product_variants($complex_or_id = null, $output = 'array')
{
    $complex_product = aff_get_product($complex_or_id, 'object');
    if(!($complex_product instanceof Complex_Product)) {
        return [];
    }

    $product_variants = $complex_product->get_variants();
    if(empty($product_variants)) {
        return [];
    }

    $product_variants = apply_filters('aff_product_variants', $product_variants, $complex_product);

    if($output == 'array') {
        $product_variants = array_map(function(Product_Variant $product_variant) {
            return Product_Helper::to_array($product_variant);
        }, $product_variants);
    }

    $product_variants = apply_filters('aff_product_formatted_variants', $product_variants, $complex_product, $output);

    return $product_variants;
}

/**
 * Get the default variant of the given product.
 *
 * @deprecated 1.1 Use 'aff_get_product_default_variant' instead
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @return null|Product_Variant The product variant or null,
 */
function aff_product_get_default_variant($complex_or_id = null)
{
    return aff_get_product_default_variant($complex_or_id, 'object');
}

/**
 * Get the default variant of the given product.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return null|array|Product_Variant The product variant in the given output format.
 */
function aff_get_product_default_variant($complex_or_id = null, $output = 'array')
{
    $complex_product = aff_get_product($complex_or_id, 'object');
    if(!($complex_product instanceof Complex_Product)) {
        return null;
    }

    $product_variant = $complex_product->get_default_variant();
    if($product_variant === null) {
        return null;
    }

    $product_variant = apply_filters('aff_product_default_variant', $product_variant, $complex_product);

    if($output == 'array') {
        $product_variant = Product_Helper::to_array($product_variant);
    }

    $product_variant = apply_filters('aff_product_formatted_default_variant', $product_variant, $complex_product, $output);

    return $product_variant;
}

/**
 * Check if the given variant is the default one
 *
 * @deprecated 1.1 Use 'aff_is_product_default_variant' instead
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @return bool Whether the product variant is the default variant of the complex parent product or not.
 */
function aff_product_is_default_variant($complex_or_id = null, $variant_or_id = null)
{
    $result = aff_is_product_default_variant($complex_or_id, $variant_or_id);

    return $result;
}

/**
 * Check if the given variant is the default one of the complex parent product.
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $complex_or_id If you pass in nothing as a complex product, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @return bool Whether the product variant is the default variant of the complex parent product or not.
 */
function aff_is_product_default_variant($complex_or_id = null, $variant_or_id = null)
{
    $complex_product = aff_get_product($complex_or_id, 'object');
    if(!($complex_product instanceof Complex_Product)) {
        return false;
    }

    $product_variant = aff_get_product($variant_or_id, 'object');
    if(!($complex_product instanceof Product_Variant)) {
        return false;
    }

    $default_variant = aff_get_product_default_variant($complex_product, 'object');

    $result = $product_variant->is_equal_to($default_variant);
    $result = apply_filters('aff_is_product_default_variant', $result, $complex_product, $product_variant);

    return $result;
}

/**
 * Get the attributes of the product variant
 *
 * @deprecated 1.1 Use 'aff_get_product_variant_attributes' instead
 * @since 0.8
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @return array The attributes.
 */
function aff_product_get_variant_attributes($product_or_id = null, $variant_or_id = null)
{
    return aff_get_product_variant_attributes($product_or_id, $variant_or_id, 'array');
}

/**
 * Get the attributes of the product variant
 *
 * @since 0.9
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @param int|string|array|\WP_Post|Product|Product_Id|null $variant_or_id If you pass in nothing as a product variant, the default variant will be used.
 * @param string $output The required return type. Either "array" or "object". Default: "array".
 * @return array|Attribute[] The attribute in the given output format.
 */
function aff_get_product_variant_attributes($product_or_id = null, $variant_or_id = null, $output = 'array')
{
    $complex_product = aff_get_product($product_or_id, 'object');
    if($complex_product instanceof Product_Variant) {
        $complex_product = $complex_product->get_parent();
    }

    if(!($complex_product instanceof Complex_Product)) {
        return [];
    }

    $product_variant = null;
    if($variant_or_id === null) {
        $product_variant = $complex_product->get_default_variant();
    } else {
        $product_variant = aff_get_product_variant($complex_product, $variant_or_id, 'object');
    }

    if($product_variant === null) {
        return [];
    }

    $attributes = $product_variant->get_attributes();
    $attributes = apply_filters('aff_product_variant_attributes', $attributes, $complex_product, $product_variant);

    if($output == 'array') {
        $attributes = array_map(function(Attribute $attribute) {
            return Attribute_Helper::to_array($attribute);
        }, $attributes);
    }

    $attributes = apply_filters('aff_product_variant_formatted_attributes', $attributes, $complex_product, $product_variant, $output);

    return $attributes;
}

/**
 * Check if the product attribute choices are enabled for the variant switching.
 *
 * @since 0.9.14
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return bool Whether the product attribute choices are enabled for the product or not.
 */
function aff_has_product_attribute_choices($product_or_id = null)
{
	$product_attribute_choices = aff_get_product_attribute_choices($product_or_id);
	if(empty($product_attribute_choices)) {
		return false;
	}

	return true;
}

/**
 * Get the product attributes choices for the variant switching.
 *
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 * @return array The attribute choices for the variant switching.
 */
function aff_get_product_attribute_choices($product_or_id = null)
{
    // Current product
    $product = aff_get_product($product_or_id, 'object');
    if($product === null) {
        return [];
    }

    // Parent product
    $parent = aff_get_product_variant_parent($product, 'object');
    if($parent === null) {
        return [];
    }

    // Product variants
    $variants = aff_get_product_variants($parent, 'object');
    if($variants === null) {
        return [];
    }

    // Current attribute
    if($product instanceof Product_Variant) {
        $current_attributes = aff_get_product_variant_attributes($parent, $product, 'array');
    } elseif($product instanceof Complex_Product) {
        $current_attributes = aff_get_product_variant_attributes($product, null, 'array');
    }

    if(empty($current_attributes)) {
        return [];
    }

    // Create the basic choices without permalinks and display
    $choices = array();
    foreach ($variants as $variant) {
        if(!$variant->has_id()) {
            continue;
        }

        $attributes = aff_get_product_variant_attributes($product, $variant, 'array');
        if(empty($attributes)) {
            continue;
        }

        foreach ($attributes as $index => $attribute) {
            if(!isset($choices[$attribute['slug']])) {
                $choices[$attribute['slug']] = array(
                    'name' => $attribute['name'],
                    'slug' => $attribute['slug'],
                    'attributes' => array(),
                );
            }

            // Get the previous and next index
            $next_index = $index + 1 < count($attributes) ? $index + 1 : $index;
            $prev_index = $index - 1 >= 0 ? $index - 1 : 0;

            $display = 'unreachable';
            if($attribute['value'] == $current_attributes[$index]['value']) {
                $display = 'selected';
            }

            if ($display == 'unreachable' && (
                ($index !== $prev_index && $attributes[$prev_index]['value'] == $current_attributes[$prev_index]['value']) ||
                ($index !== $next_index && $attributes[$next_index]['value'] == $current_attributes[$next_index]['value']))) {
                $display = 'reachable';
            }

            if( !isset($choices[$attribute['slug']]['attributes'][$attribute['value']]) ||
                ($display == 'selected' && $choices[$attribute['slug']]['attributes'][$attribute['value']]['display'] != 'selected') ||
               ($display == 'reachable' && $choices[$attribute['slug']]['attributes'][$attribute['value']]['display'] == 'unreachable')) {

                $choices[$attribute['slug']]['attributes'][$attribute['value']] = array(
                    'value' => $attribute['value'],
                    'unit' => $attribute['unit'],
                    'display' => $display,
                    'permalink' => $display == 'selected' ? null : get_permalink($variant->get_post()),
                );
            }
        }
    }

    // Remove the keys
    $choices = array_values($choices);
    foreach ($choices as $index => $choice) {
        $choices[$index]['attributes'] = array_values($choices[$index]['attributes']);
    }

    $choices = apply_filters('aff_product_attribute_choices', $choices, $parent);

    return $choices;
}

/**
 * Prints the product attributes choices for the variant switching.
 *
 * @since 0.6
 * @param int|string|array|\WP_Post|Product|Product_Id|null $product_or_id If you pass in nothing as a parameter, the current post will be used.
 */
function aff_the_product_attribute_choices($product_or_id = null)
{
	$product = aff_get_product($product_or_id);
	if($product === null) {
		return;
	}

	aff_render_template('product/attribute-choices', [
		'product' => $product,
	]);
}

/**
 * Get the shop name.
 *
 * @since 0.9
 * @param array|Shop $shop Whether to escape the output or not.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return null|string|Name The name in the given output format.
 */
function aff_get_shop_name($shop, $output = 'scalar')
{
    // Normalize the shop.
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $name = $shop->get_name();
    $name = apply_filters('aff_shop_name', $name, $shop);
    if(empty($name)) {
        return null;
    }

    if($output == 'scalar') {
        $name = $name->get_value();
    }

    $name = apply_filters('aff_shop_formatted_name', $name, $shop, $output);

    return $name;
}

/**
 * Print the shop name.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the name is taken.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_shop_name($shop = null, $escape = true)
{
    $name = aff_get_shop_name($shop, 'scalar');
    if($name == null) {
        return;
    }

    if($escape) {
        $name = esc_html($name);
    }

    echo $name;
}

/**
 * Check if the shop has a thumbnail.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the thumbnail is taken.
 * @return bool Whether the shop has a thumbnail or not.
 */
function aff_has_shop_thumbnail($shop = null)
{
    $thumbnail = aff_get_shop_thumbnail($shop, 'object');
    $result = !empty($thumbnail);

    return $result;
}

/**
 * Get the shop thumbnail.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the thumbnail is taken.
 * @param string $output The required return type. Choose from "scalar", "array" or "object". Default: "scalar".
 * @return null|int|array|Image The image in the given output format.
 */
function aff_get_shop_thumbnail($shop, $output = 'array')
{
    // Normalize the shop.
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $thumbnail = $shop->get_thumbnail();
    $thumbnail = apply_filters('aff_shop_thumbnail', $thumbnail, $shop);

    if(empty($thumbnail)) {
        return null;
    }

    if($output == 'scalar') {
        $thumbnail = $thumbnail->get_id();
    }

    if($output == 'array') {
        $thumbnail = Image_Helper::to_array($thumbnail);
    }

    $thumbnail = apply_filters('aff_shop_formatted_thumbnail', $thumbnail, $shop, $output);

    return $thumbnail;
}

/**
 * Print the shop thumbnail.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.9
 * @param array|Shop $shop
 * @param string|array $size Image size to use. Accepts any valid image size, or an array of width and height values in pixels (in that order). Default value: 'post-thumbnail'.
 * @param string|array $attr Query string or array of attributes. Default value: ''.
 */
function aff_the_shop_thumbnail($shop, $size = 'thumbnail', $attr = '')
{
    $thumbnail = aff_get_shop_thumbnail($shop, 'scalar');
    if(empty($thumbnail)) {
        return;
    }

    $result = wp_get_attachment_image($thumbnail, $size, false, $attr);

    echo $result;
}

/**
 * Get the price indication like VAT and shipping costs of the shop.
 *
 * @since 0.7
 * @param null|array|Shop $shop The shop from which the price indication is taken.
 * @param null|string $custom_text Custom text to show instead the default indication.
 * @return string The shop price indication containing the VAT and shipping costs.
 */
function aff_get_shop_price_indication($shop = null, $custom_text = null)
{
    // Normalize the shop.
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $text = !empty($custom_text) ? $custom_text : __('Incl. 19 % VAT and excl. shipping costs.', 'affilicious');
    $text = apply_filters('aff_shop_price_indication', $text, $shop);

    return $text;
}

/**
 * Print the price indication like VAT and shipping costs of the shop.
 *
 * @since 0.7
 * @param array|Shop $shop The shop from which the price indication is taken.
 * @param null|string $custom_text Custom text to show instead the default indication.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_shop_price_indication($shop = null, $custom_text = null, $escape = true)
{
    $indication = aff_get_shop_price_indication($shop, $custom_text);
    if($indication === null) {
        return;
    }

    if($escape) {
        $indication = esc_html($indication);
    }

    echo $indication;
}

/**
 * Check if the shop has an updated at price indication.
 *
 * @since 0.8.12
 * @param array|Shop $shop The shop from which the price indication is taken.
 * @return bool Whether the shop has a updated at indication or not.
 */
function aff_has_shop_updated_at_indication($shop)
{
    return !empty(aff_get_shop_updated_at_indication($shop));
}

/**
 * Get the last updated indication of the shop.
 *
 * @since 0.7
 * @param array|Shop $shop The shop from which the updated at indication is taken.
 * @param null|string $custom_text Custom text to show instead the default indication.
 * @return null|string The shop updated at indication containing the date and time of the last update.
 */
function aff_get_shop_updated_at_indication($shop, $custom_text = null)
{
    // Normalize the shop.
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $text = null;
    if($shop !== null) {
        $updated_at = Time_Helper::to_datetime_i18n($shop->get_updated_at());

        $text = sprintf(
            !empty($custom_text) ? $custom_text : __('Last updated: %s.', 'affilicious'),
            $updated_at
        );
    }

    $text = apply_filters('aff_shop_updated_at_indication', $text, $shop);

    return $text;
}

/**
 * Print the last updated indication for the shop.
 *
 * @since 0.7
 * @param array|Shop $shop The shop from which the updated at indication is taken.
 * @param null|string $custom_text Custom text to show instead the default indication. Use "%s" as placeholder for the formatted date and time.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_shop_updated_at_indication($shop, $custom_text = null, $escape = true)
{
    $indication = aff_get_shop_updated_at_indication($shop, $custom_text);
    if($indication === null) {
        return;
    }

    if($escape) {
        $indication = esc_html($indication);
    }

    echo $indication;
}

/**
 * Get the shop's availability.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the availability is taken.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return string|Availability
 */
function aff_get_shop_availability($shop, $output = 'scalar')
{
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

	if(empty($shop)) {
		$availability = Availability::out_of_stock();
	} else {
		$availability = $shop->get_pricing()->get_availability();
	}

    $availability = apply_filters('aff_shop_availability', $availability, $shop);

    if($output == 'scalar') {
        $availability = $availability->get_value();
    }

    $availability = apply_filters('aff_shop_formatted_availability', $availability, $shop, $output);

    return $availability;
}

/**
 * Check if the shop is available.
 *
 * @since 0.7
 * @param array|Shop $shop The shop from which the availability is taken.
 * @return bool Whether the shop is available or not.
 */
function aff_is_shop_available($shop)
{
    // Normalize the shop.
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    if(empty($shop)) {
    	return false;
    }

    $available = $shop->get_pricing()->get_availability()->is_available();
    $available = apply_filters('aff_is_shop_available', $available, $shop);

    return $available;
}

/**
 * Check if the shop is out of stock.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the availability is taken.
 * @return bool Whether the shop is out of stock or not.
 */
function aff_is_shop_out_of_stock($shop)
{
    // Normalize the shop.
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $out_of_stock = $shop->get_pricing()->get_availability()->is_out_of_stock();
    $out_of_stock = apply_filters('aff_is_shop_out_of_stock', $out_of_stock, $shop);

    return $out_of_stock;
}

/**
 * Check if the shop should display the old price.
 *
 * @deprecated 1.1 Don't use it anymore.
 * @since 0.8
 * @return bool
 */
function aff_should_shop_display_old_price()
{
    return true;
}

/**
 * Check if the shop contains a price.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the price is taken.
 * @return bool Whether the shop contains a price or not.
 */
function aff_has_shop_price($shop)
{
    return !empty(aff_get_shop_price($shop));
}

/**
 * Get the shop's price.
 *
 * @since 0.8.9
 * @param array|Shop $shop The shop from which the price is taken.
 * @param string $output The required return type. One of "scalar", "array" or "object". Default: "scalar".
 * @return null|string|array|Shop The shop in the given output format.
 */
function aff_get_shop_price($shop, $output = 'scalar')
{
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $price = $shop->get_pricing()->get_price();
    if($price === null) {
        return null;
    }

    $price = apply_filters('aff_shop_price', $price, $shop);

    if($output == 'scalar') {
        $price = Money_Helper::to_string($price);
    }

    if($output == 'array') {
        $price = Money_Helper::to_array($price);
    }

    $price = apply_filters('aff_shop_formatted_price', $price, $shop, $output);

    return $price;
}

/**
 * Print the formatted shop's price.
 *
 * @since 0.8.9
 * @param array|Shop $shop The shop from which the price is taken.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_shop_price($shop, $escape = true)
{
    $price = aff_get_shop_price($shop, 'scalar');
    if(empty($price)) {
        return;
    }

    if($escape) {
        $price = esc_html($price);
    }

    echo $price;
}

/**
 * Check if the shop contains an old price.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the old price is taken.
 * @return bool Whether the shop contains a old price or not.
 */
function aff_has_shop_old_price($shop)
{
    return !empty(aff_get_shop_old_price($shop));
}

/**
 * Get the shop's old price.
 *
 * @since 0.8.9
 * @param array|Shop $shop The shop from which the old price is taken.
 * @param string $output The required return type. One of "scalar", "array" or "object". Default: "scalar".
 * @return null|string|array|Shop The old price in the given output format.
 */
function aff_get_shop_old_price($shop, $output = 'scalar')
{
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $old_price = $shop->get_pricing()->get_old_price();
    if($old_price === null) {
        return null;
    }

    $old_price = apply_filters('aff_shop_old_price', $old_price, $shop);

    if($output == 'scalar') {
        $old_price = Money_Helper::to_string($old_price);
    }

    if($output == 'array') {
        $old_price = Money_Helper::to_array($old_price);
    }

    $old_price = apply_filters('aff_shop_formatted_old_price', $old_price, $shop, $output);

    return $old_price;
}

/**
 * Get the shop's affiliate link.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the affiliate link is taken.
 * @param string $output The required return type. Either "scalar" or "object". Default: "scalar".
 * @return string|Affiliate_Link
 */
function aff_get_shop_affiliate_link($shop, $output = 'scalar')
{
    if(is_array($shop)) {
        $shop = Shop_Helper::from_array($shop);
    }

    $affiliate_link = $shop->get_tracking()->get_affiliate_link();

    $affiliate_link = apply_filters('aff_shop_affiliate_link', $affiliate_link, $shop);

    if($output == 'scalar') {
        $affiliate_link = $affiliate_link->get_value();
    }

    $affiliate_link = apply_filters('aff_shop_formatted_affiliate_link', $affiliate_link, $shop, $output);

    return $affiliate_link;
}

/**
 * Print the shop's affiliate link.
 *
 * @since 0.9
 * @param array|Shop $shop The shop from which the affiliate link is taken.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_shop_affiliate_link($shop, $escape = true)
{
    $affiliate_link = aff_get_shop_affiliate_link($shop, 'scalar');
    if(empty($affiliate_link)) {
        return;
    }

    if($escape) {
        $affiliate_link = esc_html($affiliate_link);
    }

    echo $affiliate_link;
}

/**
 * Print the formatted shop's old price.
 *
 * @since 0.8.9
 * @param array|Shop $shop The shop from which old price is taken.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_shop_old_price($shop, $escape = true)
{
    $old_price = aff_get_shop_old_price($shop, 'scalar');
    if(empty($old_price)) {
        return;
    }

    if($escape) {
        $old_price = esc_html($old_price);
    }

    echo $old_price;
}

/**
 * Get the license key for the item.
 *
 * @since 0.8.12
 * @param string $item_key The item key of the software.
 * @return null|string
 */
function aff_get_license_key($item_key)
{
    /** @var \Affilicious\Common\License\License_Manager $license_manager */
    $license_manager = Affilicious::get('affilicious.common.admin.license.manager');

    $license_key = $license_manager->find_item_license_key($item_key);
    if($license_key === null) {
        return null;
    }

    $license_key = apply_filters('aff_license_key', $license_key, $item_key);

    return $license_key;
}

/**
 * Print the license key item.
 *
 * @since 0.8.12
 * @param string $item_key The item key of the software.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_license_key($item_key, $escape = true)
{
    $license_key = aff_get_license_key($item_key);

    if($escape) {
        $license_key = esc_html($license_key);
    }

    echo $license_key;
}

/**
 * Check if the license status is valid.
 *
 * @since 0.8.12
 * @param License_Status $status The status of the license processor.
 * @return bool
 */
function aff_is_license_status_active(License_Status $status)
{
    return $status->is_valid();
}

/**
 * Check if the license status is valid.
 *
 * @since 0.8.12
 * @param License_Status $status The status of the license processor.
 * @return bool
 */
function aff_is_license_status_inactive(License_Status $status)
{
    return $status->is_invalid();
}

/**
 * Check if the license status is missing.
 *
 * @since 0.8.12
 * @param License_Status $status The status of the license processor.
 * @return bool
 */
function aff_is_license_status_missing(License_Status $status)
{
    return $status->is_missing();
}

/**
 * Check if the license status is success.
 *
 * @since 0.8.12
 * @param License_Status $status The status of the license processor.
 * @return bool
 */
function aff_is_license_status_success(License_Status $status)
{
    return $status->is_success();
}

/**
 * Check if the license status is error.
 *
 * @since 0.8.12
 * @param License_Status$status The status of the license processor.
 * @return bool
 */
function aff_is_license_status_error(License_Status $status)
{
    return $status->is_error();
}

/**
 * Check if the license status has a message.
 *
 * @since 0.8.12
 * @param License_Status $status The status of the license processor.
 * @return bool
 */
function aff_has_license_status_message(License_Status $status)
{
    return $status->has_message();
}

/**
 * Get the license status message.
 *
 * @since 0.8.12
 * @param License_Status $status The status of the license processor.
 * @return null|string The license status message.
 */
function aff_get_license_status_message(License_Status $status)
{
    $message = $status->get_message();
    if($message === null) {
        return null;
    }

    $message = apply_filters('aff_license_status_message', $message, $status);

    return $message;
}

/**
 * Print the license status message.
 *
 * @since 0.8.12
 * @param License_Status $status The status of the license processor.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_license_status_message(License_Status $status, $escape = true)
{
    $message = aff_get_license_status_message($status);
    if($message === null) {
        return;
    }

    if($escape) {
        $message = esc_html($message);
    }

    echo $message;
}

/**
 * Render the template immediately.
 *
 * @since 0.9.5
 * @param string $name The name for the template.
 * @param array $params The variables for the template. Default: empty array.
 * @param bool $require Whether to require or require. Default: true.
 */
function aff_render_template($name, $params = [], $require = true)
{
	Template_Helper::render($name, $params, $require);
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
function aff_stringify_template($name, $params = [], $require = true)
{
	return Template_Helper::stringify($name, $params, $require);
}

/**
 * Get the buy button text for the product universal box.
 *
 * @since 0.9.10
 * @param null|array $shop The shop from which the data is taken.
 * @return string The buy button text.
 */
function aff_get_product_universal_box_buy_button_text($shop = null)
{
	$text = get_theme_mod('aff_universal_box-shops-button_buy_text');
	if(empty($text)) {
		$text = __('Buy now at %s', 'affilicious');
	}

	if(isset($shop['name']) && strpos($text, '%s') !== false) {
		$text = sprintf($text, $shop['name']);
	}

	return $text;
}

/**
 * Print the buy button text for the product universal box.
 *
 * @since 0.9.10
 * @param null|array $shop The shop from which the data is taken.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_universal_box_buy_button_text($shop = null, $escape = true)
{
	$text = aff_get_product_universal_box_buy_button_text($shop);

	if($escape) {
		$text = esc_html($text);
	}

	echo $text;
}

/**
 * Get the not available button text for the product universal box.
 *
 * @since 0.9.10
 * @param null|array $shop The shop from which the data is taken.
 * @return string The not available button text.
 */
function aff_get_product_universal_box_not_available_button_text($shop = null)
{
	$text = get_theme_mod('aff_universal_box-shops-button_not_available_text');
	if(empty($text)) {
		return __('Unfortunately not available', 'affilicious');
	}

	if(isset($shop['name']) && strpos($text, '%s') !== false) {
		$text = sprintf($text, $shop['name']);
	}

	return $text;
}

/**
 * Print the not available button text for the product universal box.
 *
 * @since 0.9.10
 * @param null|array $shop The shop from which the data is taken.
 * @param bool $escape Whether to escape the output or not.
 */
function aff_the_product_universal_box_not_available_button_text($shop = null, $escape = true)
{
	$text = aff_get_product_universal_box_not_available_button_text($shop);

	if($escape) {
		$text = esc_html($text);
	}

	echo $text;
}

/**
 * Check if the notice with the ID is dismissed.
 *
 * @since 0.9.16
 * @param string $dismissible_id The unique ID used for the notice.
 * @return bool Whether the notice is dismissed or not.
 */
function aff_is_notice_dismissed($dismissible_id)
{
    $option = get_option("aff_notice_{$dismissible_id}_dismissed");
    $is_dismissed = $option == 'yes';

    return $is_dismissed;
}
