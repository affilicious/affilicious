<?php
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Helper\PostHelper;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * Get the product
 * @param int|\WP_Post|Product|null $post
 * @return Product
 */
function affilicious_get_product($post = null)
{
    if ($post instanceof Product) {
        return $post;
    }

    $post = PostHelper::getPost($post);
    $productFactory = new CarbonProductRepository();
    $product = $productFactory->findById($post->ID);

    return $product;
}

/**
 * Get the product detail groups
 * @param int|\WP_Post|Product|null $product
 * @return array
 */
function affilicious_get_product_detail_groups($product = null)
{
    if ($product === null) {
        $product = affilicious_get_product();
    } elseif(!($product instanceof Product)) {
        $product = affilicious_get_product($product);
    }

    return $product->getDetailGroups();
}

/**
 * Get the plain product details of the detail groups
 * @param int|\WP_Post|Product|null $product
 * @return array
 */
function affilicious_get_product_details($product = null)
{
    if ($product === null) {
        $product = affilicious_get_product();
    } elseif(!($product instanceof Product)) {
        $product = affilicious_get_product($product);
    }

    $result = array();
    foreach ($product->getDetailGroups() as $detailGroup) {
        if(!empty($detailGroup[Product::DETAIL_GROUP_FIELDS])) {
            $result = array_merge($result, $detailGroup[Product::DETAIL_GROUP_FIELDS]);
        }
    }

    return $result;
}
