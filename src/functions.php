<?php
use Affilicious\Product\Application\Helper\ProductHelper;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Shop\Application\Helper\ShopHelper;
use Affilicious\Shop\Domain\Model\Shop;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
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
 * Get the product review rating from 0 to 5
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|float
 */
function affilicious_get_product_review_rating($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null || !$product->hasReview()) {
        return null;
    }

    $review = $product->getReview();
    $rating = $review->getRating();
    $rawRating = $rating->getValue();

    return $rawRating;
}

/**
 * Get the product review votes
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|int
 */
function affilicious_get_product_review_votes($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null || !$product->hasReview()) {
        return null;
    }

    $review = $product->getReview();
    if(!$review->hasVotes()) {
        return null;
    }

    $votes = $review->getVotes();
    $rawVotes = $votes->getValue();

    return $rawVotes;
}

/**
 * Get the plain product details of the detail groups.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|array
 */
function affilicious_get_product_details($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $details = $product->getDetails();

    $rawDetails = array();
    foreach ($details as $detail) {
        $rawDetail = array(
            'key' => $detail->getKey()->getValue(),
            'type' => $detail->getType()->getValue(),
            'name' => $detail->getName()->getValue(),
            'value' => $detail->hasValue() ? $detail->getValue()->getValue() : null,
            'unit' => $detail->hasUnit() ? $detail->getUnit()->getValue() : null,
        );

        $rawDetails[] = $rawDetail;
    }

    return $rawDetails;
}

/**
 * Get the product image gallery by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|array
 */
function affilicious_get_product_image_gallery($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $images = $product->getImageGallery();

    $rawImages = array();
    foreach ($images as $image) {
        $rawImage = array(
            'id' => $image->getId()->getValue(),
            'src' => $image->getSource()->getValue(),
            'width' => $image->hasWidth() ? $image->getWidth()->getValue() : null,
            'height' => $image->hasHeight() ? $image->getHeight()->getValue() : null,
        );

        $rawImages[] = $rawImage;
    }

    return $rawImages;
}

/**
 * Get the shops by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|array
 */
function affilicious_get_product_shops($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $shops = $product->getShops();

    $rawShops = array();
    foreach ($shops as $shop) {
        $rawShop = array(
            'shop_id' =>  $shop->getId()->getValue(),
            'title' =>  $shop->getTitle()->getValue(),
            'thumbnail' => !$shop->hasThumbnail() ? null : array(
                'id' => $shop->getThumbnail()->getId()->getValue(),
                'src' => $shop->getThumbnail()->getSource()->getValue(),
                'width' => $shop->getThumbnail()->hasWidth() ? $shop->getThumbnail()->getWidth()->getValue() : null,
                'height' => $shop->getThumbnail()->hasHeight() ? $shop->getThumbnail()->getHeight()->getValue() : null,
            ),
            'price' => !$shop->hasPrice() ? null : array(
                'value' => $shop->getPrice()->getValue(),
                'currency' => array(
                    'value' => $shop->getPrice()->getCurrency()->getValue(),
                    'label' => $shop->getPrice()->getCurrency()->getLabel(),
                    'symbol' => $shop->getPrice()->getCurrency()->getSymbol(),
                ),
            ),
            'old_price' => !$shop->hasOldPrice() ? null : array(
                'value' => $shop->getOldPrice()->getValue(),
                'currency' => array(
                    'value' => $shop->getOldPrice()->getCurrency()->getValue(),
                    'label' => $shop->getOldPrice()->getCurrency()->getLabel(),
                    'symbol' => $shop->getOldPrice()->getCurrency()->getSymbol(),
                ),
            ),
            'affiliate_id' => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
            'affiliate_link' => $shop->hasAffiliateLink() ? $shop->getAffiliateLink()->getValue() : null,
        );

        $rawShops[] = $rawShop;
    }

    return $rawShops;
}

/**
 * Get the related products by the product.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|int[]
 */
function affilicious_get_product_related_products($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $relatedProducts = $product->getRelatedProducts();

    $rawRelatedProducts = array();
    foreach ($relatedProducts as $relatedProduct) {
        $rawRelatedProduct = $relatedProduct->getValue();
        $rawRelatedProducts[] = $rawRelatedProduct;
    }

    return $rawRelatedProducts;
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
 * @return null|int[]
 */
function affilicious_get_product_related_accessories($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $relatedAccessories = $product->getRelatedAccessories();

    $rawRelatedAccessories = array();
    foreach ($relatedAccessories as $relatedAccessory) {
        $rawRelatedProduct = $relatedAccessory->getValue();
        $rawRelatedAccessories[] = $rawRelatedProduct;
    }

    return $rawRelatedAccessories;
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
 * Get the product link.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|string
 */
function affilicious_get_product_link($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

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
 * @return null|array
 */
function affilicious_get_product_shop($productOrId = null, $shopOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $shop = ProductHelper::getShop($product, $shopOrId);
    if($shop === null) {
        return null;
    }

    $rawShop = array(
        'shop_id' => $shop->getId()->getValue(),
        'title' => $shop->getTitle()->getValue(),
        'thumbnail' => !$shop->hasThumbnail() ? null : array(
            'id' => $shop->getThumbnail()->getId()->getValue(),
            'src' => $shop->getThumbnail()->getSource()->getValue(),
            'width' => $shop->getThumbnail()->hasWidth() ? $shop->getThumbnail()->getWidth()->getValue() : null,
            'height' => $shop->getThumbnail()->hasHeight() ? $shop->getThumbnail()->getHeight()->getValue() : null,
        ),
        'price' => !$shop->hasPrice() ? null : array(
            'value' => $shop->getPrice()->getValue(),
            'currency' => array(
                'value' => $shop->getPrice()->getCurrency()->getValue(),
                'label' => $shop->getPrice()->getCurrency()->getLabel(),
                'symbol' => $shop->getPrice()->getCurrency()->getSymbol(),
            ),
        ),
        'old_price' => !$shop->hasOldPrice() ? null : array(
            'value' => $shop->getOldPrice()->getValue(),
            'currency' => array(
                'value' => $shop->getOldPrice()->getCurrency()->getValue(),
                'label' => $shop->getOldPrice()->getCurrency()->getLabel(),
                'symbol' => $shop->getOldPrice()->getCurrency()->getSymbol(),
            ),
        ),
        'affiliate_id' => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
        'affiliate_link' => $shop->hasAffiliateLink() ? $shop->getAffiliateLink()->getValue() : null,
    );

    return $rawShop;
}

/**
 * Get the cheapest shop of the given product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.5.1
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|array
 */
function affilicious_get_product_cheapest_shop($productOrId = null)
{
    $product = affilicious_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $shop = $product->getCheapestShop();
    if($shop === null) {
        return null;
    }

    $rawShop = array(
        'shop_id' => $shop->getId()->getValue(),
        'title' => $shop->getTitle()->getValue(),
        'thumbnail' => !$shop->hasThumbnail() ? null : array(
            'id' => $shop->getThumbnail()->getId()->getValue(),
            'src' => $shop->getThumbnail()->getSource()->getValue(),
            'width' => $shop->getThumbnail()->hasWidth() ? $shop->getThumbnail()->getWidth()->getValue() : null,
            'height' => $shop->getThumbnail()->hasHeight() ? $shop->getThumbnail()->getHeight()->getValue() : null,
        ),
        'price' => !$shop->hasPrice() ? null : array(
            'value' => $shop->getPrice()->getValue(),
            'currency' => array(
                'value' => $shop->getPrice()->getCurrency()->getValue(),
                'label' => $shop->getPrice()->getCurrency()->getLabel(),
                'symbol' => $shop->getPrice()->getCurrency()->getSymbol(),
            ),
        ),
        'old_price' => !$shop->hasOldPrice() ? null : array(
            'value' => $shop->getOldPrice()->getValue(),
            'currency' => array(
                'value' => $shop->getOldPrice()->getCurrency()->getValue(),
                'label' => $shop->getOldPrice()->getCurrency()->getLabel(),
                'symbol' => $shop->getOldPrice()->getCurrency()->getSymbol(),
            ),
        ),
        'affiliate_id' => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
        'affiliate_link' => $shop->hasAffiliateLink() ? $shop->getAffiliateLink()->getValue() : null,
    );

    return $rawShop;
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
    if($product === null) {
        return null;
    }

    $shop = ProductHelper::getShop($product, $shopOrId);
    if (empty($shop)) {
        return null;
    }

    $price = $shop->getPrice();
    if($price === null) {
        return null;
    }

    $rawPrice = $price->getValue() . ' ' . $price->getCurrency()->getSymbol();

    return $rawPrice;
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
    if($product === null) {
        return null;
    }

    $shop = $product->getCheapestShop();
    if (empty($shop)) {
        return null;
    }

    $price = $shop->getPrice();
    if($price === null) {
        return null;
    }

    $rawPrice = $price->getValue() . ' ' . $price->getCurrency()->getSymbol();

    return $rawPrice;
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
 *
 * This function is just wrapper for get_the_post_thumbnail:
 * https://developer.wordpress.org/reference/functions/get_the_post_thumbnail/
 *
 * @since 0.3
 * @param int|\WP_Post|Shop|array|null $post
 * @param string|array $size
 * @param string|array $attr
 * @return null|string
 */
function affilicious_get_shop_thumbnail($post = null, $size = 'post-thumbnail', $attr = '')
{
    if(method_exists($post, 'getRawPost')) {
        $post = $post->getRawPost();
    }

    if(is_array($post) && !empty($post['shop_id'])) {
        $post = get_post($post['shop_id']);
    }

    if (!($post instanceof WP_Post) && !is_int($post)) {
        return null;
    }

    $thumbnail = get_the_post_thumbnail($post, $size, $attr);

    return $thumbnail;
}
