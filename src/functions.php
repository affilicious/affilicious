<?php
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group;
use Affilicious\Detail\Application\Helper\Attribute_Template_Group_Helper;
use Affilicious\Detail\Application\Helper\Detail_Template_Group_Helper;
use Affilicious\Detail\Domain\Model\Detail_Template_Group;
use Affilicious\Product\Application\Helper\Product_Helper;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Product\Domain\Model\Variant\Product_Variant;
use Affilicious\Shop\Application\Helper\Shop_Template_Helper;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Shop_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
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
 * Check if post, the post with the ID or the current post is a product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return bool
 */
function aff_is_product($product_or_id = null)
{
    $product = Product_Helper::get_product($product_or_id);

    return $product !== null;
}

/**
 * Get the product by the _wordpress ID or post.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @return Product
 */
function aff_get_product($product_or_id = null)
{
    $product = Product_Helper::get_product($product_or_id);

    return $product;
}

/**
 * Get the product review rating from 0 to 5
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|float
 */
function aff_get_product_review_rating($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null || !$product->has_review()) {
        return null;
    }

    $review = $product->get_review();
    $rating = $review->get_rating();
    $raw_rating = $rating->get_value();

    return $raw_rating;
}

/**
 * Get the product review votes
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|int
 */
function aff_get_product_review_votes($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null || !$product->has_review()) {
        return null;
    }

    $review = $product->get_review();
    if(!$review->has_votes()) {
        return null;
    }

    $votes = $review->get_votes();
    $raw_votes = $votes->get_value();

    return $raw_votes;
}

/**
 * Get the plain product details of the detail groups.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|array
 */
function aff_get_product_details($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $detail_groups = $product->get_detail_groups();

    $raw_details = array();
    foreach ($detail_groups as $detail_group) {
        $details = $detail_group->get_details();

        foreach ($details as $detail) {
            $raw_detail = array(
                'title' => $detail->get_title()->get_value(),
                'name' => $detail->get_name()->get_value(),
                'key' => $detail->get_key()->get_value(),
                'type' => $detail->get_type()->get_value(),
                'unit' => $detail->has_unit() ? $detail->get_unit()->get_value() : null,
                'value' => $detail->has_value() ? $detail->get_value()->get_value() : null,
            );

            $raw_details[] = $raw_detail;
        }
    }

    return $raw_details;
}

/**
 * Get the product image gallery by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|array
 */
function aff_get_product_image_gallery($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $images = $product->get_image_gallery();

    $raw_images = array();
    foreach ($images as $image) {
        $raw_image = array(
            'id' => $image->get_id()->get_value(),
            'src' => $image->get_source()->get_value(),
            'width' => $image->has_width() ? $image->get_width()->get_value() : null,
            'height' => $image->has_height() ? $image->get_height()->get_value() : null,
        );

        $raw_images[] = $raw_image;
    }

    return $raw_images;
}

/**
 * Get the shops by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|array
 */
function aff_get_product_shops($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $shops = $product->get_shops();

    $raw_shops = array();
    foreach ($shops as $shop) {
        $raw_shop = array(
            'shop_template_id' => $shop->has_template_id() ? $shop->get_template_id()->get_value() : null,
            'title' => $shop->get_title()->get_value(),
            'affiliate_link' => $shop->get_affiliate_link()->get_value(),
            'affiliate_id' => $shop->has_affiliate_id() ? $shop->get_affiliate_id()->get_value() : null,
            'thumbnail' => !$shop->has_thumbnail() ? null : array(
                'id' => $shop->get_thumbnail()->get_id()->get_value(),
                'src' => $shop->get_thumbnail()->get_source()->get_value(),
                'width' => $shop->get_thumbnail()->has_width() ? $shop->get_thumbnail()->get_width()->get_value() : null,
                'height' => $shop->get_thumbnail()->has_height() ? $shop->get_thumbnail()->get_height()->get_value() : null,
            ),
            'price' => !$shop->has_price() ? null : array(
                'value' => $shop->get_price()->get_value(),
                'currency' => array(
                    'value' => $shop->get_price()->get_currency()->get_value(),
                    'label' => $shop->get_price()->get_currency()->get_label(),
                    'symbol' => $shop->get_price()->get_currency()->get_symbol(),
                ),
            ),
            'old_price' => !$shop->has_old_price() ? null : array(
                'value' => $shop->get_old_price()->get_value(),
                'currency' => array(
                    'value' => $shop->get_old_price()->get_currency()->get_value(),
                    'label' => $shop->get_old_price()->get_currency()->get_label(),
                    'symbol' => $shop->get_old_price()->get_currency()->get_symbol(),
                ),
            ),
        );

        $raw_shops[] = $raw_shop;
    }

    return $raw_shops;
}

/**
 * Get the related products by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|int[]
 */
function aff_get_product_related_products($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $related_products = $product->get_related_products();

    $raw_related_products = array();
    foreach ($related_products as $related_product) {
        $raw_related_product = $related_product->get_value();

        $raw_related_products[] = $raw_related_product;
    }

    return $raw_related_products;
}

/**
 * Get the query of the related products by the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @param array $args
 * @return null|WP_Query
 */
function aff_get_product_related_products_query($product_or_id = null, $args = array())
{
    $related_product_ids = aff_get_product_related_products($product_or_id);
    if (empty($related_product_ids)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => Product::POST_TYPE,
        'post__in' => $related_product_ids,
        'order_by' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the related accessories by the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|int[]
 */
function aff_get_product_related_accessories($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $related_accessories = $product->get_related_accessories();

    $raw_related_accessories = array();
    foreach ($related_accessories as $related_accessory) {
        $raw_related_product = $related_accessory->get_value();
        $raw_related_accessories[] = $raw_related_product;
    }

    return $raw_related_accessories;
}

/**
 * Get the query of the related accessories by the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @param array $args
 * @return null|WP_Query
 */
function aff_get_product_related_accessories_query($product_or_id = null, $args = array())
{
    $related_accessories_ids = aff_get_product_related_accessories($product_or_id);
    if (empty($related_accessories_ids)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => Product::POST_TYPE,
        'post__in' => $related_accessories_ids,
        'order_by' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the product link.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|string
 */
function aff_get_product_link($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $link = get_permalink($product->get_raw_post());
    if(empty($link)) {
        return null;
    }

    return $link;
}

/**
 * Get the shop of the given product.
 * If you pass in nothing as a product, the current post will be used.
 * If you pass in nothing as an affiliate link, the cheapest shop will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @param string|Affiliate_Link|null $affiliate_link
 * @return null|array
 */
function aff_get_product_shop($product_or_id = null, $affiliate_link = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
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

    $raw_shop = array(
        'shop_template_id' => $shop->has_template_id() ? $shop->get_template_id()->get_value() : null,
        'title' => $shop->get_title()->get_value(),
        'affiliate_link' => $shop->get_affiliate_link()->get_value(),
        'affiliate_id' => $shop->has_affiliate_id() ? $shop->get_affiliate_id()->get_value() : null,
        'thumbnail' => !$shop->has_thumbnail() ? null : array(
            'id' => $shop->get_thumbnail()->get_id()->get_value(),
            'src' => $shop->get_thumbnail()->get_source()->get_value(),
            'width' => $shop->get_thumbnail()->has_width() ? $shop->get_thumbnail()->get_width()->get_value() : null,
            'height' => $shop->get_thumbnail()->has_height() ? $shop->get_thumbnail()->get_height()->get_value() : null,
        ),
        'price' => !$shop->has_price() ? null : array(
            'value' => $shop->get_price()->get_value(),
            'currency' => array(
                'value' => $shop->get_price()->get_currency()->get_value(),
                'label' => $shop->get_price()->get_currency()->get_label(),
                'symbol' => $shop->get_price()->get_currency()->get_symbol(),
            ),
        ),
        'old_price' => !$shop->has_old_price() ? null : array(
            'value' => $shop->get_old_price()->get_value(),
            'currency' => array(
                'value' => $shop->get_old_price()->get_currency()->get_value(),
                'label' => $shop->get_old_price()->get_currency()->get_label(),
                'symbol' => $shop->get_old_price()->get_currency()->get_symbol(),
            ),
        ),
    );

    return $raw_shop;
}

/**
 * Get the cheapest shop of the given product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.5.1
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|array
 */
function aff_get_product_cheapest_shop($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $shop = $product->get_cheapest_shop();
    if($shop === null) {
        return null;
    }

    $raw_shop = array(
        'shop_template_id' => $shop->has_template_id() ? $shop->get_template_id()->get_value() : null,
        'title' => $shop->get_title()->get_value(),
        'affiliate_link' => $shop->get_affiliate_link()->get_value(),
        'affiliate_id' => $shop->has_affiliate_id() ? $shop->get_affiliate_id()->get_value() : null,
        'thumbnail' => !$shop->has_thumbnail() ? null : array(
            'id' => $shop->get_thumbnail()->get_id()->get_value(),
            'src' => $shop->get_thumbnail()->get_source()->get_value(),
            'width' => $shop->get_thumbnail()->has_width() ? $shop->get_thumbnail()->get_width()->get_value() : null,
            'height' => $shop->get_thumbnail()->has_height() ? $shop->get_thumbnail()->get_height()->get_value() : null,
        ),
        'price' => !$shop->has_price() ? null : array(
            'value' => $shop->get_price()->get_value(),
            'currency' => array(
                'value' => $shop->get_price()->get_currency()->get_value(),
                'label' => $shop->get_price()->get_currency()->get_label(),
                'symbol' => $shop->get_price()->get_currency()->get_symbol(),
            ),
        ),
        'old_price' => !$shop->has_old_price() ? null : array(
            'value' => $shop->get_old_price()->get_value(),
            'currency' => array(
                'value' => $shop->get_old_price()->get_currency()->get_value(),
                'label' => $shop->get_old_price()->get_currency()->get_label(),
                'symbol' => $shop->get_old_price()->get_currency()->get_symbol(),
            ),
        ),
    );

    return $raw_shop;
}

/**
 * Get the price with the currency of the product.
 * If you pass in nothing as a product, the current post will be used.
 * If you pass in nothing as an affiliate link, the cheapest shop will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @param string|Affiliate_Link|null $affiliate_link
 * @return null|string
 */
function aff_get_product_price($product_or_id = null, $affiliate_link = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
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
    if (empty($shop)) {
        return null;
    }

    $price = $shop->get_price();
    if($price === null) {
        return null;
    }

    $raw_price = $price->get_value() . ' ' . $price->get_currency()->get_symbol();

    return $raw_price;
}

/**
 * Get the cheapest price with the currency of the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.5.1
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|string
 */
function aff_get_product_cheapest_price($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $shop = $product->get_cheapest_shop();
    if (empty($shop)) {
        return null;
    }

    $price = $shop->get_price();
    if($price === null) {
        return null;
    }

    $raw_price = $price->get_value() . ' ' . $price->get_currency()->get_symbol();

    return $raw_price;
}

/**
 * Get the affiliate link by the product and shop
 * If you pass in nothing as a product, the current post will be used.
 * If you pass in nothing as a shop, the first shop will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $product_or_id
 * @param int|\WP_Post|Shop_Template|null $shop_or_id
 * @return null|string
 */
function aff_get_product_affiliate_link($product_or_id = null, $shop_or_id = null)
{
    $shop = aff_get_product_shop($product_or_id, $shop_or_id);
    if(empty($shop)) {
        return null;
    }

    $affiliate_link = $shop['affiliate_link'];

    return $affiliate_link;
}

/**
 * Get the affiliate link by the product and shop
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.5.1
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|string
 */
function aff_get_product_cheapest_affiliate_link($product_or_id = null)
{
    $shop = aff_get_product_cheapest_shop($product_or_id);
    if(empty($shop)) {
        return null;
    }

    $affiliate_link = $shop['affiliate_link'];

    return $affiliate_link;
}

/**
 * Check if the product is of the given type.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param string|Type $type
 * @param int|\WP_Post|Product|null $product_or_id
 * @return bool
 */
function aff_product_is_type($type, $product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return false;
    }

    if($type instanceof Type) {
        $type = $type->get_value();
    }

    return $product->get_type()->get_value() == $type;
}

/**
 * Check if the product is a simple product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return bool
 */
function aff_product_is_simple($product_or_id = null)
{
    return aff_product_is_type(Type::simple(), $product_or_id);
}

/**
 * Check if the product is a complex product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return bool
 */
function aff_product_is_complex($product_or_id = null)
{
    return aff_product_is_type(Type::complex(), $product_or_id);
}

/**
 * Check if the product is a product variant.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return bool
 */
function aff_product_is_variant($product_or_id = null)
{
    return aff_product_is_type(Type::variant(), $product_or_id);
}

/**
 * Get the parent of the product variant.
 * If the given product is already the parent, it will be returned instead.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|Product
 */
function aff_product_get_parent($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    if(aff_product_is_complex($product)) {
        return $product;
    }

    if(aff_product_is_variant($product)) {
        /** @var Product_Variant $product */
        $parent = $product->get_parent();

        return $parent;
    }

    return null;
}

/**
 * Check if the given product contains the variant
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @param int|\WP_Post|Product|null $variant_or_id
 * @return bool
 */
function aff_product_has_variant($product_or_id = null, $variant_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return false;
    }

    if(aff_product_is_complex($product)) {
        return false;
    }

    $variant = aff_get_product($variant_or_id);
    if($variant === null) {
        return false;
    }

    $result = $product->has_variant($variant->get_name());

    return $result;
}

/**
 * Get the product variants of the given product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|Product_variant[]
 */
function aff_product_get_variants($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    if(!aff_product_is_complex($product)) {
        return null;
    }

    $variants = $product->get_variants();

    return $variants;
}

/**
 * Get the default variant of the given product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|Product_variant
 */
function aff_product_get_default_variant($product_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    if(!aff_product_is_complex($product)) {
        return null;
    }

    $default_variant = $product->get_default_variant();

    return $default_variant;
}

/**
 * Check if the given variant is the default one
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @param int|\WP_Post|Product|null $variant_or_id
 * @return bool
 */
function aff_product_is_default_variant($product_or_id = null, $variant_or_id = null) {

    $product = aff_get_product($product_or_id);
    if($product === null) {
        return false;
    }

    $variant = aff_get_product($variant_or_id);
    if($variant === null) {
        return false;
    }

    $default_variant = aff_product_get_default_variant($product);

    return $variant->is_equal_to($default_variant);
}

/**
 * Get the attribute group of the product variant
 * If you pass in nothing as a product, the current post will be used.
 * If you pass in nothing as a variant, the default variant will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @param int|\WP_Post|Product|null $variant_or_id
 * @return null|array
 */
function aff_product_get_variant_attribute__group($product_or_id = null, $variant_or_id = null)
{
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    $variant = null;
    if($variant_or_id === null) {
        $variant = $product->get_default_variant();
    } else {
        if($variant_or_id instanceof Product_Variant) {
            $variant = $variant_or_id;
        } elseif(!aff_product_has_variant($product, $variant_or_id)) {
            $variant = aff_get_product($variant_or_id);
        }
    }

    if($variant === null) {
        return null;
    }

    $attribute_group = $variant->get_attribute_group();
    $attributes = $attribute_group->get_attributes();

    $raw_attribute_group = array(
        'title' => $attribute_group->get_title()->get_value(),
        'name' => $attribute_group->get_name()->get_value(),
        'key' => $attribute_group->get_key()->get_value(),
        'attributes' => array(),
    );

    foreach ($attributes as $attribute) {
        $raw_attribute_group['attributes'][] = array(
            'title' => $attribute->get_title()->get_value(),
            'name' => $attribute->get_name()->get_value(),
            'key' => $attribute->get_key()->get_value(),
            'value' => $attribute->get_value()->get_value(),
            'type' => $attribute->get_type()->get_value(),
            'unit' => $attribute->has_unit() ? $attribute->get_unit()->get_value() : null,
        );
    }

    return $raw_attribute_group;
}

/**
 * Get the product attributes choices
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 * @return null|array
 */
function aff_get_product_attribute_choices($product_or_id = null)
{
    // Current product
    $product = aff_get_product($product_or_id);
    if($product === null) {
        return null;
    }

    // Parent product
    $parent = aff_product_get_parent($product);
    if($parent === null) {
        return null;
    }

    // Product variants
    $variants = aff_product_get_variants($parent);
    if($variants === null) {
        return null;
    }

    // Current attribute group
    $current_attribute_group = null;
    if(aff_product_is_variant($product)) {
        $current_attribute_group = aff_product_get_variant_attribute__group($parent, $product);
    } elseif(aff_product_is_complex($product)) {
        $current_attribute_group = aff_product_get_variant_attribute__group($product);
    }

    if($current_attribute_group === null) {
        return null;
    }

    // Create the basic choices without permalinks and display
    $choices = array();
    foreach ($variants as $variant) {
        if(!$variant->has_id()) {
            continue;
        }

        $attribute_group = aff_product_get_variant_attribute__group($product, $variant);
        $attributes = $attribute_group['attributes'];
        $current_attributes = $current_attribute_group['attributes'];

        foreach ($attributes as $index => $attribute) {
            if(!isset($choices[$attribute['name']])) {
                $choices[$attribute['name']] = array(
                    'title' => $attribute['title'],
                    'name' => $attribute['name'],
                    'key' => $attribute['key'],
                    'choices' => array(),
                );
            }

            // Get the previous and next index
            $next_index = $index + 1 < count($attributes) ? $index + 1 : $index;
            $prev_index = $index - 1 >= 0 ? $index - 1 : 0;

            $display = 'unreachable';
            if($attribute['value'] == $current_attributes[$index]['value']) {
                $display = 'current';
            }

            if ($display == 'unreachable' && (
                ($index !== $prev_index && $attributes[$prev_index]['value'] == $current_attributes[$prev_index]['value']) ||
                ($index !== $next_index && $attributes[$next_index]['value'] == $current_attributes[$next_index]['value']))) {
                $display = 'reachable';
            }

            if( !isset($choices[$attribute['name']]['choices'][$attribute['value']]) ||
                ($display == 'current' && $choices[$attribute['name']]['choices'][$attribute['value']]['display'] != 'current') ||
               ($display == 'reachable' && $choices[$attribute['name']]['choices'][$attribute['value']]['display'] == 'unreachable')) {

                $choices[$attribute['name']]['choices'][$attribute['value']] = array(
                    'value' => $attribute['value'],
                    'unit' => $attribute['unit'],
                    'display' => $display,
                    'permalink' => $display == 'current' ? '#' : get_permalink($variant->get_raw_post()),
                );
            }
        }
    }

    // Remove the keys
    $choices = array_values($choices);
    foreach ($choices as $index => $choice) {
        $choices[$index]['choices'] = array_values($choices[$index]['choices']);
    }

    return $choices;
}

/**
 * Prints the product attributes choices to the screen
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $product_or_id
 */
function aff_the_product_attribute_choices($product_or_id = null)
{
    $attribute_choices = aff_get_product_attribute_choices($product_or_id);
    if(empty($attribute_choices)) {
        return;
    }

    foreach ($attribute_choices as $name => $attribute_choice) {
        echo '<div class="aff-product-attribute-choices">';
        echo '<h5>' . $attribute_choice['title'] . '</h5>';
        echo '<ul class="aff-product-attribute-choice-list" data-attribute-name="' . $name . '">';

        foreach ($attribute_choice['choices'] as $choice) {
            echo '<li class="aff-product-attribute-choices-item ' . $choice['display'] . '">';
            echo '<a href="' . $choice['permalink'] .'">' . $choice['value'] . ' ' . $choice['unit'] . '</a>';
            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
    }
}

/**
 * Get the shop template by the ID or _wordpress post.
 * If you pass in nothing as a shop template, the current post will be used.
 *
 * @since 0.6
 * @param int|array|\WP_Post|Shop_Template|null $shop_or_id
 * @return Shop_Template
 */
function aff_getShop_Template($shop_or_id = null)
{
    $shop = Shop_Template_Helper::get_shop_template($shop_or_id);

    return $shop;
}

/**
 * Get the detail template group by the ID or _wordpress post.
 * If you pass in nothing as a detail template group template, the current post will be used.
 *
 * @since 0.6
 * @param int|array|\WP_Post|Detail_Template_Group|null $detail_template_group_or_id
 * @return Detail_Template_Group
 */
function aff_get_detail_template_group($detail_template_group_or_id = null)
{
    $detail_template_group = Detail_Template_Group_Helper::get_detail_template_group($detail_template_group_or_id);

    return $detail_template_group;
}

/**
 * Get the attribute template group by the ID or _wordpress post.
 * If you pass in nothing as a attribute template group template, the current post will be used.
 *
 * @since 0.6
 * @param int|array|\WP_Post|Attribute_Template_Group|null $attribute_template_group_or_id
 * @return Attribute_Template_Group
 */
function aff_getAttribute_Template_Group($attribute_template_group_or_id = null)
{
    $attribute_template_group = Attribute_Template_Group_Helper::getAttribute_Template_Group($attribute_template_group_or_id);

    return $attribute_template_group;
}
