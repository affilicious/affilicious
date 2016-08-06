<?php
use Affilicious\ProductsPlugin\Product\Domain\Helper\PostHelper;
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\Shop;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * Get the product by the ID or Wordpress post.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return Product
 */
function affilicious_get_product($post = null)
{
    $container = AffiliciousProductsPlugin::getContainer();
    $productRepository = $container['product_repository'];

    $post = PostHelper::getPost($post);
    $product = $productRepository->findById($post->ID);

    return $product;
}

/**
 * Get the product detail groups by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return array
 */
function affilicious_get_product_detail_groups($post = null)
{
    $product = affilicious_get_product($post);

    return $product->getDetailGroups();
}

/**
 * Get the plain product details of the detail groups.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return array
 */
function affilicious_get_product_details($post = null)
{
    $product = affilicious_get_product($post);

    $result = array();
    foreach ($product->getDetailGroups() as $detailGroup) {
        if (!empty($detailGroup[Product::DETAIL_GROUP_FIELDS])) {
            $result = array_merge($result, $detailGroup[Product::DETAIL_GROUP_FIELDS]);
        }
    }

    return $result;
}

/**
 * Check if the products has an European Article Number (EAN)
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return bool
 */
function affilicious_has_product_ean($post = null)
{
    $product = affilicious_get_product($post);
    $flag = $product->hasEan();

    return $flag;
}

/**
 * Get the European Article Number (EAN) by the product
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return bool
 */
function affilicious_get_product_ean($post = null)
{
    $product = affilicious_get_product($post);
    $ean = $product->getEan();

    return apply_filters('affilicious_get_product_ean', $product->getId(), $ean);
}

/**
 * Get the product shops.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return array
 */
function affilicious_get_product_shops($post = null)
{
    $product = affilicious_get_product($post);

    return $product->getShops();
}

/**
 * Get the related products by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return int[]
 */
function affilicious_get_product_related_products($post = null)
{
    $product = affilicious_get_product($post);
    $relatedProducts = $product->getRelatedProducts();

    return apply_filters('affilicious_get_product_related_products', $product->getId(), $relatedProducts);
}

/**
 * Get the query of the related products by the product
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @param array $args
 * @return null|WP_Query
 */
function affilicious_get_product_related_products_query($post = null, $args = array())
{
    $relatedProductIds = affilicious_get_product_related_products($post);
    if (empty($relatedProductIds)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => array(Product::POST_TYPE),
        'post__in' => $relatedProductIds,
        'orderBy' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the related accessories by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @return int[]
 */
function affilicious_get_product_related_accessories($post = null)
{
    $product = affilicious_get_product($post);
    $relatedAccessories = $product->getRelatedAccessories();

    return apply_filters('affilicious_get_product_related_accessories', $product->getId(), $relatedAccessories);
}

/**
 * Get the query of the related accessories by the product
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @param array $args
 * @return null|WP_Query
 */
function affilicious_get_product_related_accessories_query($post = null, $args = array())
{
    $relatedAccessoriesIds = affilicious_get_product_related_accessories($post);
    if (empty($relatedAccessoriesIds)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => array(Product::POST_TYPE),
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
 * @param int|\WP_Post|Product|null $post
 * @return int[]
 */
function affilicious_get_product_related_posts($post = null)
{
    $product = affilicious_get_product($post);
    $relatedPosts = $product->getRelatedPosts();

    return apply_filters('affilicious_get_product_related_posts', $product->getId(), $relatedPosts);
}

/**
 * Get the query of the related posts by the product
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $post
 * @param array $args
 * @return null|WP_Query
 */
function affilicious_get_product_related_posts_query($post = null, $args = array())
{
    $relatedPostsIds = affilicious_get_product_related_posts($post);
    if (empty($relatedPostsIds)) {
        return null;
    }

    $options = wp_parse_args($args, array(
        'post_type' => array('post'),
        'post__in' => $relatedPostsIds,
        'orderBy' => 'ASC',
    ));

    $query = new \WP_Query($options);

    return $query;
}

/**
 * Get the shop by the ID or Wordpress post.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Shop|null $post
 * @return Shop
 */
function affilicious_get_shop($post = null)
{
    $container = AffiliciousProductsPlugin::getContainer();
    $post = PostHelper::getPost($post);

    $shopRepository = $container['shop_repository'];
    $shop = $shopRepository->findById($post->ID);

    return $shop;
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
    if (!is_string($currency)) {
        return false;
    }

    $currencyLabel = ucwords($currency, '-');
    $currencyLabel = strpos($currencyLabel, 'Us-') === 0 ? str_replace('Us-', 'US-', $currencyLabel) : $currencyLabel;
    $currencyLabel = __($currencyLabel, 'affiliciousproducts');

    return $currencyLabel;
}
