<?php
use Affilicious\Product\Domain\Helper\ShopHelper;
use Affilicious\Product\Domain\Helper\ProductHelper;
use Affilicious\Product\Domain\Helper\PriceHelper;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Shop;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * Get the product by the Wordpress ID or post.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return Product
 */
function affilicious_get_product($productOrId = null)
{
    $product = ProductHelper::getProduct($productOrId);

    return $product;
}

/**
 * Get the product number of ratings
 *
 * @since 0.3.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return int
 */
function affilicious_get_product_number_of_ratings($productOrId = null)
{
	$product = ProductHelper::getProduct($productOrId);
	$numberOfRatings = $product->getNumberOfRatings();

	return $numberOfRatings;
}

/**
 * Get the product star rating from 0 to 5 in 0.5 steps
 *
 * @since 0.3.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return float
 */
function affilicious_get_product_star_rating($productOrId = null)
{
	$product = ProductHelper::getProduct($productOrId);
	$starRating = $product->getStarRating();

	return $starRating;
}

/**
 * Get the product detail groups by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return array
 */
function affilicious_get_product_detail_groups($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $detailGroups = $product->getDetailGroups();

    return $detailGroups;
}

/**
 * Get the plain product details of the detail groups.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return array
 */
function affilicious_get_product_details($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $details = ProductHelper::getDetails($product);

    return $details;
}

/**
 * Check if the products has an European Article Number (EAN)
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return bool
 */
function affilicious_has_product_ean($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $flag = $product->hasEan();

    return $flag;
}

/**
 * Get the European Article Number (EAN) by the product
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return string
 */
function affilicious_get_product_ean($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $ean = $product->getEan();

    return $ean;
}

/**
 * Get the product image gallery by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return int[]
 */
function affilicious_get_product_image_gallery($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $imageGallery = $product->getImageGallery();

    return $imageGallery;
}

/**
 * Get the shops by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return array
 */
function affilicious_get_product_shops($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $shops = $product->getShops();

    return $shops;
}

/**
 * Get the related products by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return int[]
 */
function affilicious_get_product_related_products($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $relatedProducts = $product->getRelatedProducts();

    return $relatedProducts;
}

/**
 * Get the query of the related products by the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @param array $args
 * @return null|WP_Query
 */
function affilicious_get_product_related_products_query($productOrId = null, $args = array())
{
    $relatedProductIds = affilicious_get_product_related_products($productOrId);
    if (empty($relatedProductIds)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => Product::POST_TYPE,
        'post__in' => $relatedProductIds,
        'orderBy' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the related accessories by the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return int[]
 */
function affilicious_get_product_related_accessories($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $relatedAccessories = $product->getRelatedAccessories();

    return $relatedAccessories;
}

/**
 * Get the query of the related accessories by the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @param array $args
 * @return null|WP_Query
 */
function affilicious_get_product_related_accessories_query($productOrId = null, $args = array())
{
    $relatedAccessoriesIds = affilicious_get_product_related_accessories($productOrId);
    if (empty($relatedAccessoriesIds)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => Product::POST_TYPE,
        'post__in' => $relatedAccessoriesIds,
        'orderBy' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the related posts by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return int[]
 */
function affilicious_get_product_related_posts($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $relatedPosts = $product->getRelatedPosts();

    return $relatedPosts;
}

/**
 * Get the query of the related posts by the product
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @param array $args
 * @return null|WP_Query
 */
function affilicious_get_product_related_posts_query($productOrId = null, $args = array())
{
    $relatedPostsIds = affilicious_get_product_related_posts($productOrId);
    if (empty($relatedPostsIds)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => 'post',
        'post__in' => $relatedPostsIds,
        'orderBy' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the product link.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|string
 */
function affilicious_get_product_link($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $link = get_permalink($product->getRawPost());
    if(empty($link)) {
        return null;
    }

    return $link;
}

/**
 * Get the shop of the given product.
 * If you pass in nothing as a product, the current post will be used.
 * If you pass in nothing as a shop, the first shop will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @param int|\WP_Post|Shop|null $shopOrId
 * @return array|null
 */
function affilicious_get_product_shop($productOrId = null, $shopOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $shop = ProductHelper::getShop($product, $shopOrId);

    return $shop;
}

/**
 * Get the cheapest shop of the given product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.5.1
 * @param int|\WP_Post|Product|null $productOrId
 * @return array|null
 */
function affilicious_get_product_cheapest_shop($productOrId = null)
{
	$product = affilicious_get_product($productOrId);
	$shop = $product->getCheapestShop();

	return $shop;
}

/**
 * Get the price with the currency of the product.
 * If you pass in nothing as a product, the current post will be used.
 * If you pass in nothing as a shop, the first shop will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @param int|\WP_Post|Shop|null $shopOrId
 * @return null|string
 */
function affilicious_get_product_price($productOrId = null, $shopOrId = null)
{
    $product = affilicious_get_product($productOrId);
    $shop = ProductHelper::getShop($product, $shopOrId);
    if (empty($shop)) {
        return null;
    }

    $value = $shop['price'];
    $currency = $shop['currency'];
    $price = affilicious_get_price($value, $currency);

    return $price;
}

/**
 * Get the cheapest price with the currency of the product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.5.1
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|string
 */
function affilicious_get_product_cheapest_price($productOrId = null)
{
	$product = affilicious_get_product($productOrId);
	$shop = $product->getCheapestShop();
	if (empty($shop)) {
		return null;
	}

	$value = $shop['price'];
	$currency = $shop['currency'];
	$price = affilicious_get_price($value, $currency);

	return $price;
}

/**
 * Get the affiliate link by the product and shop
 * If you pass in nothing as a product, the current post will be used.
 * If you pass in nothing as a shop, the first shop will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @param int|\WP_Post|Shop|null $shopOrId
 * @return null|string
 */
function affilicious_get_product_affiliate_link($productOrId = null, $shopOrId = null)
{
    $shop = affilicious_get_product_shop($productOrId, $shopOrId);
	if(empty($shop)) {
		return null;
	}

    $affiliateLink = $shop['affiliate_link'];

    return $affiliateLink;
}

/**
 * Get the affiliate link by the product and shop
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.5.1
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|string
 */
function affilicious_get_product_cheapest_affiliate_link($productOrId = null)
{
	$shop = affilicious_get_product_cheapest_shop($productOrId);
	if(empty($shop)) {
		return null;
	}

	$affiliateLink = $shop['affiliate_link'];

	return $affiliateLink;
}

/**
 * Check if the current page is a product.
 *
 * @since 0.3
 * @return bool
 */
function affilicious_is_product()
{
    return is_singular(Product::POST_TYPE);
}

/**
 * Get the shop by the ID or Wordpress post.
 * If you pass in nothing as a shop, the current post will be used.
 *
 * @since 0.3
 * @param int|array|\WP_Post|Shop|null $shopOrId
 * @return Shop
 */
function affilicious_get_shop($shopOrId = null)
{
    $shop = ShopHelper::getShop($shopOrId);

    return $shop;
}

/**
 * Print the shop thumbnail.
 * If you pass in nothing as a parameter, the current post will be used.
 * This function is just wrapper for get_the_post_thumbnail:
 * https://developer.wordpress.org/reference/functions/get_the_post_thumbnail/
 *
 * @since 0.3
 * @param int|\WP_Post|Shop|null $post
 * @param string|array $size
 * @param string|array $attr
 * @return null|string
 */
function affilicious_get_shop_thumbnail($post = null, $size = 'post-thumbnail', $attr = '')
{
    if ($post instanceof Shop) {
        $post = $post->getRawPost();
    }

    if (!($post instanceof WP_Post) && !is_int($post)) {
        return null;
    }

    $thumbnail = get_the_post_thumbnail($post, $size, $attr);

    return $thumbnail;
}

/**
 * Get the label for the currency option.
 *
 * @since 0.3
 * @param string $currency
 * @return bool|string
 */
function affilicious_get_currency_label($currency)
{
    $currencyLabel = PriceHelper::getCurrencyLabel($currency);

    return $currencyLabel;
}

/**
 * Get the symbol for the currency option
 *
 * @since 0.3
 * @param string $currency
 * @return string
 */
function affilicious_get_currency_symbol($currency)
{
    $currencySymbol = PriceHelper::getCurrencySymbol($currency);

    return $currencySymbol;
}

/**
 * Get the price with the correct currency.
 * If the value or currency is invalid, this functions returns false.
 *
 * @since 0.3
 * @param string|int $value
 * @param string $currency
 * @return string|null
 */
function affilicious_get_price($value, $currency)
{
    $currencySymbol = PriceHelper::getPrice($value, $currency);

    return $currencySymbol;
}
