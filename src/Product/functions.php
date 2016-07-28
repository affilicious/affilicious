<?php
use Affilicious\ProductsPlugin\Product\Product;
use Affilicious\ProductsPlugin\Auxiliary\PostHelper;
use Affilicious\ProductsPlugin\Product\ProductFactory;
use Affilicious\ProductsPlugin\Product\Field\FieldGroup;
use Affilicious\ProductsPlugin\Product\Detail\DetailGroup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * @param int|\WP_Post $post
 * @return Product
 */
function ap_get_product($post = null)
{
    $post = PostHelper::getPost($post);
    $productFactory = new ProductFactory();
    $product = $productFactory->create($post);

    return $product;
}

/**
 * @param Product|null $product
 * @return FieldGroup[]
 */
function ap_get_product_field_groups(Product $product = null)
{
    if ($product === null) {
        $post = PostHelper::getPost();
        $product = $post;
    }

    return $product->getFieldGroups();
}

/**
 * @param Product|null $product
 * @return DetailGroup[]
 */
function ap_get_product_detail_groups(Product $product = null)
{
    if ($product === null) {
        $post = PostHelper::getPost();
        $product = $post;
    }

    return $product->getDetailGroups();
}
