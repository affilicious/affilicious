<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\ImageId;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Common\Infrastructure\Persistence\Carbon\AbstractCarbonRepository;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupId;
use Affilicious\Product\Domain\Model\DetailGroup\DetailGroup;
use Affilicious\Product\Domain\Model\DetailGroup\DetailGroupFactoryInterface;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;
use Affilicious\Product\Domain\Model\Review\Rating;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Review\Votes;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Shop\ShopFactoryInterface;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Affilicious\Shop\Domain\Model\ShopTemplateId;
use Affilicious\Shop\Domain\Model\ShopTemplateRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class AbstractCarbonProductRepository extends AbstractCarbonRepository implements ProductRepositoryInterface
{
    const TYPE = 'affilicious_product_type';
    const SHOPS = 'affilicious_product_shops';
    const SHOP_TEMPLATE_ID = 'shop_template_id';
    const SHOP_PRICE = 'price';
    const SHOP_OLD_PRICE = 'old_price';
    const SHOP_CURRENCY = 'currency';
    const SHOP_AFFILIATE_ID = 'affiliate_id';
    const SHOP_AFFILIATE_LINK = 'affiliate_link';
    const DETAIL_GROUPS = 'affilicious_product_detail_groups';
    const DETAIL_TEMPLATE_GROUP_ID = 'detail_template_group_id';
    const VARIANTS = 'affilicious_product_variants';
    const VARIANT_TITLE = 'title';
    const VARIANT_ATTRIBUTE_GROUPS = 'attribute_groups';
    const VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID = 'attribute_template_group_id';
    const VARIANT_THUMBNAIL = 'thumbnail';
    const VARIANT_SHOPS = 'shops';
    const REVIEW_RATING = 'affilicious_product_review_rating';
    const REVIEW_VOTES = 'affilicious_product_review_votes';
    const RELATED_PRODUCTS = 'affilicious_product_related_products';
    const RELATED_ACCESSORIES = 'affilicious_product_related_accessories';
    const IMAGE_GALLERY = '_affilicious_product_image_gallery';

    // TODO: Remove the legacy support in the beta
    const SHOP_ID = 'shop_id';
    const DETAIL_GROUP_ID = 'detail_group_id';
    const VARIANT_ATTRIBUTE_GROUP_ID = 'attribute_group_id';

    /**
     * @var DetailGroupFactoryInterface
     */
    protected $detailGroupFactory;

    /**
     * @var ShopTemplateRepositoryInterface
     */
    protected $shopFactory;

    /**
     * @since 0.6
     * @param DetailGroupFactoryInterface $detailGroupFactory
     * @param ShopFactoryInterface $shopFactory
     */
    public function __construct(
        DetailGroupFactoryInterface $detailGroupFactory,
        ShopFactoryInterface $shopFactory
    )
    {
        $this->detailGroupFactory = $detailGroupFactory;
        $this->shopFactory = $shopFactory;
    }

    /**
     * Add the type like simple or variants to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addType(Product $product, \WP_Post $post)
    {
        $type = carbon_get_post_meta($post->ID, self::TYPE);
        if(!empty($type)) {
            $product->setType(new Type($type));
        }

        return $product;
    }

    /**
     * Add the thumbnail to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addThumbnail(Product $product, \WP_Post $post)
    {
        $thumbnailId = get_post_thumbnail_id($post->ID);
        if (!empty($thumbnailId)) {
            $thumbnail = self::getImageFromAttachmentId($thumbnailId);

            if($thumbnail !== null) {
                $product->setThumbnail($thumbnail);
            }
        }

        return $product;
    }

    /**
     * Add the content to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addContent(Product $product, \WP_Post $post)
    {
        $content = $post->post_content;
        if(!empty($content)) {
            $product->setContent(new Content($content));
        }

        return $product;
    }

    /**
     * Add shops to the product
     *
     * @since 0.6
     * @param Product $product
     * @param array $rawShops
     * @return Product
     */
    protected function addShops(Product $product, $rawShops = array())
    {
        if(empty($rawShops)) {
            $rawShops = carbon_get_post_meta($product->getId()->getValue(), self::SHOPS, 'complex');
        }

        if (!empty($rawShops)) {
            foreach ($rawShops as $rawShop) {
                $shop = self::getShopFromArray($rawShop);

                if ($shop !== null) {
                    $product->addShop($shop);
                }
            }
        }

        return $product;
    }

    /**
     * Add detail groups to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addDetailGroups(Product $product, \WP_Post $post)
    {
        $rawDetailGroups = carbon_get_post_meta($post->ID, self::DETAIL_GROUPS, 'complex');
        if (!empty($rawDetailGroups)) {
            foreach ($rawDetailGroups as $rawDetailGroup) {
                $detailGroup = self::getDetailGroupFromArray($rawDetailGroup);

                if (!empty($detailGroup)) {
                    $product->addDetailGroup($detailGroup);
                }
            }
        }

        return $product;
    }

    /**
     * Add the review to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addReview(Product $product, \WP_Post $post)
    {
        $rating = carbon_get_post_meta($post->ID, self::REVIEW_RATING);
        if(!empty($rating) && $rating !== 'none') {
            $review = new Review(new Rating($rating));

            $votes = carbon_get_post_meta($post->ID, self::REVIEW_VOTES);
            if (!empty($votes)) {
                $review->setVotes(new Votes($votes));
            }

            $product->setReview($review);
        }

        return $product;
    }

    /**
     * Add related products to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addRelatedProducts(Product $product, \WP_Post $post)
    {
        $relatedProducts = carbon_get_post_meta($post->ID, self::RELATED_PRODUCTS);
        if (!empty($relatedProducts)) {
            $relatedProducts = array_map(function ($value) {
                return new ProductId($value);
            }, $relatedProducts);

            $product->setRelatedProducts($relatedProducts);
        }

        return $product;
    }

    /**
     * Add related accessories to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addRelatedAccessories(Product $product, \WP_Post $post)
    {
        $relatedAccessories = carbon_get_post_meta($post->ID, self::RELATED_ACCESSORIES);
        if (!empty($relatedAccessories)) {
            $relatedAccessories = array_map(function ($value) {
                return new ProductId($value);
            }, $relatedAccessories);

            $product->setRelatedAccessories($relatedAccessories);
        }

        return $product;
    }

    /**
     * Add the image gallery to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addImageGallery(Product $product, \WP_Post $post)
    {
        $imageGallery = get_post_meta($post->ID, self::IMAGE_GALLERY);
        if (!empty($imageGallery)) {
            $imageIds = explode(',', $imageGallery[0]);

            $images = array();
            foreach ($imageIds as $imageId) {
                $image = self::getImageFromAttachmentId($imageId);

                if($image !== null) {
                    $images[] = $image;
                }
            }

            $product->setImageGallery($images);
        }

        return $product;
    }

    /**
     * Build the shop from the raw array
     *
     * @since 0.6
     * @param array $rawShop
     * @return null|Shop
     */
    protected function getShopFromArray(array $rawShop)
    {
        $shopTemplateId = !empty($rawShop[self::SHOP_TEMPLATE_ID]) ? intval($rawShop[self::SHOP_TEMPLATE_ID]) : null;

        // TODO: Remove the legacy support in the beta
        if(empty($shopTemplateId)) {
            $shopTemplateId = !empty($rawShop[self::SHOP_ID]) ? intval($rawShop[self::SHOP_ID]) : null;
        }

        if (empty($shopTemplateId)) {
            return null;
        }

        $shop = $this->shopFactory->createFromTemplateIdAndData(
            new ShopTemplateId($shopTemplateId),
            $rawShop
        );

        return $shop;
    }

    /**
     * Build the detail group from the raw array
     *
     * @since 0.6
     * @param array $rawDetailGroup
     * @return null|DetailGroup
     */
    protected function getDetailGroupFromArray(array $rawDetailGroup)
    {
        $detailTemplateGroupId = !empty($rawDetailGroup[self::DETAIL_TEMPLATE_GROUP_ID]) ? intval($rawDetailGroup[self::DETAIL_TEMPLATE_GROUP_ID]) : null;

        // TODO: Remove the legacy support in the beta
        if(empty($detailTemplateGroupId)) {
            $detailTemplateGroupId = !empty($rawDetailGroup[self::DETAIL_GROUP_ID]) ? intval($rawDetailGroup[self::DETAIL_GROUP_ID]) : null;
        }

        if (empty($detailTemplateGroupId)) {
            return null;
        }

        $detailGroup = $this->detailGroupFactory->createFromTemplateIdAndData(
            new DetailTemplateGroupId($detailTemplateGroupId),
            $rawDetailGroup
        );

        return $detailGroup;
    }

    /**
     * Build the image object from the array
     *
     * @since 0.6
     * @param int $attachmentId
     * @return null|Image
     */
    protected function getImageFromAttachmentId($attachmentId)
    {
        $attachment = wp_get_attachment_image_src($attachmentId);
        if(empty($attachment) && count($attachment) == 0) {
            return null;
        }

        $source = $attachment[0];
        if(empty($source)) {
            return null;
        }

        $image = new Image(
            new ImageId($attachmentId),
            new Source($source)
        );

        $width = $attachment[1];
        if(!empty($width)) {
            $image->setWidth(new Width($width));
        }

        $height = $attachment[2];
        if(!empty($height)) {
            $image->setHeight(new Height($height));
        }

        return $image;
    }

    /**
     * Get the thumbnail image from the post
     *
     * @since 0.6
     * @param int $postId
     * @return null|Image
     */
    protected function getThumbnailImageFromPostId($postId)
    {
        $thumbnailId = get_post_thumbnail_id($postId);
        if (!empty($thumbnailId)) {
            $thumbnail = self::getImageFromAttachmentId($thumbnailId);

            return $thumbnail;
        }

        return null;
    }

    /**
     * Store the type like simple or variants for the product
     *
     * @since 0.6
     * @param Product $product
     */
    protected function storeType(Product $product)
    {
        $this->storePostMeta($product->getId(), self::TYPE, $product->getType());
    }

    /**
     * Store the shops for the product
     *
     * @since 0.6
     * @param Product $product
     * @param string $metaKey
     */
    protected function storeShops(Product $product, $metaKey)
    {
        $shops = $product->getShops();

        $carbonShops = array();
        foreach ($shops as $shop) {
            if(!isset($carbonShops[$shop->getKey()->getValue()])) {
                $carbonShops[$shop->getKey()->getValue()] = array();
            }

            $carbonShops[$shop->getKey()->getValue()][] = array(
                self::SHOP_TEMPLATE_ID => $shop->hasTemplateId() ? $shop->getTemplateId()->getValue() : null,
                self::SHOP_AFFILIATE_ID => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
                self::SHOP_AFFILIATE_LINK => $shop->getAffiliateLink()->getValue(),
                self::SHOP_PRICE => $shop->hasPrice() ? $shop->getPrice()->getValue() : null,
                self::SHOP_CURRENCY => $shop->getCurrency()->getValue(),
                self::SHOP_OLD_PRICE => $shop->hasOldPrice() ? $shop->getOldPrice()->getValue() : null,
            );
        }

        $carbonMetaKeys = $this->buildComplexCarbonMetaKey($carbonShops, $metaKey);
        foreach ($carbonMetaKeys as $carbonMetaKey => $carbonMetaValue) {
            if($carbonMetaValue !== null && $product->hasId()) {
                $this->storePostMeta($product->getId(), $carbonMetaKey, $carbonMetaValue);
            }
        }
    }

    /**
     * Store the thumbnail for the product
     *
     * @since 0.6
     * @param Product $product
     */
    protected function storeThumbnail(Product $product)
    {
        if(!$product->hasThumbnail()) {
            return;
        }

        $this->storePostMeta($product->getId(), self::THUMBNAIL_ID, $product->getThumbnail()->getId());
    }

    /**
     * Build the default args from the saved product in the database
     *
     * @since 0.6
     * @param Product $product
     * @return array
     */
    protected function getDefaultArgs(Product $product)
    {
        $defaultArgs = array();
        if($product->hasId()) {
            $defaultArgs = get_post($product->getId()->getValue(), ARRAY_A);
        }

        return $defaultArgs;
    }

    /**
     * Build the args to save the product or product variant
     *
     * @since 0.6
     * @param Product $product
     * @param array $defaultArgs
     * @return array
     */
    protected function getArgs(Product $product, array $defaultArgs = array())
    {
        $args = wp_parse_args(array(
            'post_title' => $product->getTitle()->getValue(),
            'post_status' => 'publish',
            'post_name' => $product->getName()->getValue(),
            'post_type' => Product::POST_TYPE,
        ), $defaultArgs);

        if($product->hasId()) {
            $args['ID'] = $product->getId()->getValue();
        }

        if($product->hasContent()) {
            $args['post_content'] = $product->getContent()->getValue();
        }

        if($product instanceof ProductVariant) {
            $args['post_parent'] = $product->getParent()->getId()->getValue();
        }

        return $args;
    }
}
