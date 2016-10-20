<?php
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroup;
use Affilicious\Detail\Application\Helper\AttributeTemplateGroupHelper;
use Affilicious\Detail\Application\Helper\DetailTemplateGroupHelper;
use Affilicious\Detail\Domain\Model\DetailTemplateGroup;
use Affilicious\Product\Application\Helper\ProductHelper;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Affilicious\Shop\Application\Helper\ShopTemplateHelper;
use Affilicious\Shop\Domain\Model\ShopTemplate;
use Affilicious\Product\Domain\Model\Type;

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
 * @param int|\WP_Post|Product|null $productOrId
 * @return bool
 */
function aff_is_product($productOrId = null)
{
    $product = ProductHelper::getProduct($productOrId);

    return $product !== null;
}

/**
 * Get the product by the Wordpress ID or post.
 * If you pass in nothing as a parameter, the current post will be used.
 *
 * @since 0.3
 * @param int|\WP_Post|Product|null $productOrId
 * @return Product
 */
function aff_get_product($productOrId = null)
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
function aff_get_product_review_rating($productOrId = null)
{
    $product = aff_get_product($productOrId);
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
function aff_get_product_review_votes($productOrId = null)
{
    $product = aff_get_product($productOrId);
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
function aff_get_product_details($productOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $details = $product->getDetails();

    $rawDetails = array();
    foreach ($details as $detail) {
        $rawDetail = array(
            'title' => $detail->getTitle()->getValue(),
            'name' => $detail->getName()->getValue(),
            'key' => $detail->getKey()->getValue(),
            'type' => $detail->getType()->getValue(),
            'unit' => $detail->hasUnit() ? $detail->getUnit()->getValue() : null,
            'value' => $detail->hasValue() ? $detail->getValue()->getValue() : null,
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
function aff_get_product_image_gallery($productOrId = null)
{
    $product = aff_get_product($productOrId);
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
function aff_get_product_shops($productOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $shops = $product->getShops();

    $rawShops = array();
    foreach ($shops as $shop) {
        $rawShop = array(
            'shop_template_id' => $shop->hasTemplateId() ? $shop->getTemplateId()->getValue() : null,
            'title' => $shop->getTitle()->getValue(),
            'affiliate_link' => $shop->getAffiliateLink()->getValue(),
            'affiliate_id' => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
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
function aff_get_product_related_products($productOrId = null)
{
    $product = aff_get_product($productOrId);
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
function aff_get_product_related_products_query($productOrId = null, $args = array())
{
    $relatedProductIds = aff_get_product_related_products($productOrId);
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
function aff_get_product_related_accessories($productOrId = null)
{
    $product = aff_get_product($productOrId);
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
function aff_get_product_related_accessories_query($productOrId = null, $args = array())
{
    $relatedAccessoriesIds = aff_get_product_related_accessories($productOrId);
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
function aff_get_product_link($productOrId = null)
{
    $product = aff_get_product($productOrId);
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
 * @param int|\WP_Post|ShopTemplate|null $shopOrId
 * @return null|array
 */
function aff_get_product_shop($productOrId = null, $shopOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $shop = ProductHelper::getShop($product, $shopOrId);
    if($shop === null) {
        return null;
    }

    $rawShop = array(
        'shop_template_id' => $shop->hasTemplateId() ? $shop->getTemplateId()->getValue() : null,
        'title' => $shop->getTitle()->getValue(),
        'affiliate_link' => $shop->getAffiliateLink()->getValue(),
        'affiliate_id' => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
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
function aff_get_product_cheapest_shop($productOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return null;
    }

    $shop = $product->getCheapestShop();
    if($shop === null) {
        return null;
    }

    $rawShop = array(
        'shop_template_id' => $shop->hasTemplateId() ? $shop->getTemplateId()->getValue() : null,
        'title' => $shop->getTitle()->getValue(),
        'affiliate_link' => $shop->getAffiliateLink()->getValue(),
        'affiliate_id' => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
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
 * @param int|\WP_Post|ShopTemplate|null $shopOrId
 * @return null|string
 */
function aff_get_product_price($productOrId = null, $shopOrId = null)
{
    $product = aff_get_product($productOrId);
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
function aff_get_product_cheapest_price($productOrId = null)
{
    $product = aff_get_product($productOrId);
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
 * @param int|\WP_Post|ShopTemplate|null $shopOrId
 * @return null|string
 */
function aff_get_product_affiliate_link($productOrId = null, $shopOrId = null)
{
    $shop = aff_get_product_shop($productOrId, $shopOrId);
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
function aff_get_product_cheapest_affiliate_link($productOrId = null)
{
    $shop = aff_get_product_cheapest_shop($productOrId);
    if(empty($shop)) {
        return null;
    }

    $affiliateLink = $shop['affiliate_link'];

    return $affiliateLink;
}

/**
 * Check if the product is of the given type.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param string|Type $type
 * @param int|\WP_Post|Product|null $productOrId
 * @return bool
 */
function aff_product_is_type($type, $productOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return false;
    }

    if($type instanceof Type) {
        $type = $type->getValue();
    }

    return $product->getType()->getValue() == $type;
}

/**
 * Check if the product is a simple product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return bool
 */
function aff_product_is_simple($productOrId = null)
{
    return aff_product_is_type(Type::simple(), $productOrId);
}

/**
 * Check if the product is a complex product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return bool
 */
function aff_product_is_complex($productOrId = null)
{
    return aff_product_is_type(Type::simple(), $productOrId);
}

/**
 * Check if the product is a product variant.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return bool
 */
function aff_product_is_variant($productOrId = null)
{
    return aff_product_is_type(Type::simple(), $productOrId);
}

/**
 * Get the parent of the product variant.
 * If the given product is already the parent, it will be returned instead.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|Product
 */
function aff_product_get_parent($productOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return null;
    }

    if(aff_product_is_complex($product)) {
        return $product;
    }

    if(aff_product_is_variant($product)) {
        /** @var ProductVariant $product */
        $parent = $product->getParent();

        return $parent;
    }

    return null;
}

/**
 * Check if the given product contains any variants.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return bool
 */
function aff_product_has_variants($productOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return false;
    }

    if(aff_product_is_variant($product)) {
        return false;
    }

    $variants = $product->getVariants();

    return empty($variants);
}

/**
 * Get the product variants of the given product.
 * If you pass in nothing as a product, the current post will be used.
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|ProductVariant[]
 */
function aff_product_get_variants($productOrId = null)
{
    $product = aff_get_product($productOrId);
    if($product === null) {
        return null;
    }

    if(!aff_product_is_variant($product)) {
        return null;
    }

    $variants = $product->getVariants();

    return $variants;
}

/**
 * Get the product attributes of the given product
 *
 * @since 0.6
 * @param int|\WP_Post|Product|null $productOrId
 * @return null|array
 */
function aff_get_product_attributes($productOrId = null)
{
    $product = aff_product_get_parent($productOrId);
    if($product === null) {
        return null;
    }

    $variants = $product->getVariants();
    $rawAttributes = array();
    foreach ($variants as $index => $variant) {
        $attributeGroup = $variant->getAttributeGroup();
        $attributes = $attributeGroup->getAttributes();

        foreach ($attributes as $attribute) {
            $rawAttributes[$index][] = array(
                'title' => $attribute->getTitle()->getValue(),
                'name' => $attribute->getName()->getValue(),
                'key' => $attribute->getKey()->getValue(),
                'type' => $attribute->getType()->getValue(),
                'unit' => $attribute->hasUnit() ? $attribute->getUnit()->getValue() : null,
                'value' => $attribute->getValue()->getValue(),
            );
        }
    }

    return $rawAttributes;
}

/**
 * Get the shop template by the ID or Wordpress post.
 * If you pass in nothing as a shop template, the current post will be used.
 *
 * @since 0.6
 * @param int|array|\WP_Post|ShopTemplate|null $shopOrId
 * @return ShopTemplate
 */
function aff_get_shop_template($shopOrId = null)
{
    $shop = ShopTemplateHelper::getShopTemplate($shopOrId);

    return $shop;
}

/**
 * Get the detail template group by the ID or Wordpress post.
 * If you pass in nothing as a detail template group template, the current post will be used.
 *
 * @since 0.6
 * @param int|array|\WP_Post|DetailTemplateGroup|null $detailTemplateGroupOrId
 * @return DetailTemplateGroup
 */
function aff_get_detail_template_group($detailTemplateGroupOrId = null)
{
    $detailTemplateGroup = DetailTemplateGroupHelper::getDetailTemplateGroup($detailTemplateGroupOrId);

    return $detailTemplateGroup;
}

/**
 * Get the attribute template group by the ID or Wordpress post.
 * If you pass in nothing as a attribute template group template, the current post will be used.
 *
 * @since 0.6
 * @param int|array|\WP_Post|AttributeTemplateGroup|null $attributeTemplateGroupOrId
 * @return AttributeTemplateGroup
 */
function aff_get_attribute_template_group($attributeTemplateGroupOrId = null)
{
    $attributeTemplateGroup = AttributeTemplateGroupHelper::getAttributeTemplateGroup($attributeTemplateGroupOrId);

    return $attributeTemplateGroup;
}
