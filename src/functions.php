<?php
use Affilicious\ProductsPlugin\Product\Product;
use Affilicious\ProductsPlugin\Auxiliary\PostHelper;
use Affilicious\ProductsPlugin\Product\ProductFactory;
use Affilicious\ProductsPlugin\Product\Field\FieldGroup;
use Affilicious\ProductsPlugin\Product\Detail\DetailGroup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * Get the product
 * @param int|\WP_Post|Product $post
 * @return Product
 */
function affilicious_get_product($post = null)
{
    if ($post instanceof Product) {
        return $post;
    }

    $post = PostHelper::getPost($post);
    $productFactory = new ProductFactory();
    $product = $productFactory->create($post);

    return $product;
}

/**
 * Get the product field groups
 * @param Product|null $product
 * @return FieldGroup[]
 */
function affilicious_get_product_field_groups($product = null)
{
    if ($product === null) {
        $product = affilicious_get_product();
    }

    return $product->getFieldGroups();
}

/**
 * Get the product detail groups
 * @param Product|null $product
 * @return DetailGroup[]
 */
function affilicious_get_product_detail_groups($product = null)
{
    if ($product === null) {
        $product = affilicious_get_product();
    }

    return $product->getDetailGroups();
}

function affilicious_get_price_comparison($product = null)
{
    if ($product === null) {
        $product = affilicious_get_product();
    }

    $product->getPriceComparison();
}
