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
 * Get the product field groups
 * @param int|\WP_Post|Product|null $product
 * @return array
 */
function affilicious_get_product_field_groups($product = null)
{
    if ($product === null) {
        $product = affilicious_get_product();
    } elseif(!($product instanceof Product)) {
        $product = affilicious_get_product($product);
    }

    return $product->getFieldGroups();
}

/**
 * Get the plain product fields of the field groups
 * @param int|\WP_Post|Product|null $product
 * @return array
 */
function affilicious_get_product_fields($product = null)
{
    if ($product === null) {
        $product = affilicious_get_product();
    } elseif(!($product instanceof Product)) {
        $product = affilicious_get_product($product);
    }

    $result = array();
    foreach ($product->getFieldGroups() as $fieldGroup) {
        if(!empty($fieldGroup[Product::FIELD_GROUP_FIELDS])) {
            $result = array_merge($result, $fieldGroup[Product::FIELD_GROUP_FIELDS]);
        }
    }

    return $result;
}
