<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\ImageId;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Persistence\Carbon\AbstractCarbonRepository;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupId;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupRepositoryInterface;
use Affilicious\Product\Domain\Model\Detail\Detail;
use Affilicious\Product\Domain\Model\Detail\Type as DetailType;
use Affilicious\Product\Domain\Model\Detail\Unit;
use Affilicious\Product\Domain\Model\Detail\Value;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;
use Affilicious\Product\Domain\Model\Review\Rating;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Review\Votes;
use Affilicious\Product\Domain\Model\Shop\AffiliateId;
use Affilicious\Product\Domain\Model\Shop\AffiliateLink;
use Affilicious\Product\Domain\Model\Shop\Currency;
use Affilicious\Product\Domain\Model\Shop\Price;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Shop\ShopFactoryInterface;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Affilicious\Shop\Domain\Model\ShopTemplateRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class AbstractCarbonProductRepository extends AbstractCarbonRepository implements ProductRepositoryInterface
{
    const TYPE = 'affilicious_product_type';
    const SHOPS = 'affilicious_product_shops';
    const SHOP_ID = 'shop_id';
    const SHOP_PRICE = 'price';
    const SHOP_OLD_PRICE = 'old_price';
    const SHOP_CURRENCY = 'currency';
    const SHOP_AFFILIATE_ID = 'affiliate_id';
    const SHOP_AFFILIATE_LINK = 'affiliate_link';
    const DETAIL_GROUPS = 'affilicious_product_detail_groups';
    const DETAIL_GROUPS_ID = 'detail_group_id';
    const VARIANTS = 'affilicious_product_variants';
    const VARIANT_ID = 'variant_id';
    const VARIANT_TITLE = 'title';
    const VARIANT_ATTRIBUTE_GROUPS = 'attribute_groups';
    const VARIANT_ATTRIBUTE_GROUPS_ID = 'attribute_group_id';
    const VARIANT_THUMBNAIL = 'thumbnail';
    const VARIANT_SHOPS = 'shops';
    const REVIEW_RATING = 'affilicious_product_review_rating';
    const REVIEW_VOTES = 'affilicious_product_review_votes';
    const RELATED_PRODUCTS = 'affilicious_product_related_products';
    const RELATED_ACCESSORIES = 'affilicious_product_related_accessories';
    const IMAGE_GALLERY = '_affilicious_product_image_gallery';

    /**
     * @var DetailTemplateGroupRepositoryInterface
     */
    protected $detailGroupRepository;

    /**
     * @var ShopTemplateRepositoryInterface
     */
    protected $shopFactory;

    /**
     * @since 0.6
     * @param DetailTemplateGroupRepositoryInterface $detailGroupRepository
     * @param ShopFactoryInterface $shopFactory
     */
    public function __construct(
        DetailTemplateGroupRepositoryInterface $detailGroupRepository,
        ShopFactoryInterface $shopFactory
    )
    {
        $this->detailGroupRepository = $detailGroupRepository;
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
     * Add details to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addDetails(Product $product, \WP_Post $post)
    {
        $detailGroups = carbon_get_post_meta($post->ID, self::DETAIL_GROUPS, 'complex');
        if (!empty($detailGroups)) {
            foreach ($detailGroups as $detailGroup) {
                $details = self::getDetailsFromArray($detailGroup);

                if (!empty($details)) {
                    $product->setDetails($details);
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
     * Build the shop object from the raw array
     *
     * @since 0.6
     * @param array $rawShop
     * @return null|Shop
     */
    protected function getShopFromArray(array $rawShop)
    {
        $shopId = !empty($rawShop[self::SHOP_ID]) ? intval($rawShop[self::SHOP_ID]) : null;
        if (empty($shopId)) {
            return null;
        }

        $title = get_the_title($shopId);
        $thumbnail = $this->getThumbnailImageFromPostId($shopId);
        $affiliateId = !empty($rawShop[self::SHOP_AFFILIATE_ID]) ? $rawShop[self::SHOP_AFFILIATE_ID] : null;
        $affiliateLink = !empty($rawShop[self::SHOP_AFFILIATE_LINK]) ? $rawShop[self::SHOP_AFFILIATE_LINK] : null;
        $price = !empty($rawShop[self::SHOP_PRICE]) ? floatval($rawShop[self::SHOP_PRICE]) : null;
        $oldPrice = !empty($rawShop[self::SHOP_OLD_PRICE]) ? floatval($rawShop[self::SHOP_OLD_PRICE]) : null;
        $currency = !empty($rawShop[self::SHOP_CURRENCY]) ? $rawShop[self::SHOP_CURRENCY] : null;

        if(empty($title) || empty($affiliateId) || empty($currency)) {
            return null;
        }

        $shop = $this->shopFactory->create(
            new Title($title),
            new AffiliateId($affiliateId),
            new Currency($currency)
        );

        if(!empty($thumbnail)) {
            $shop->setThumbnail($thumbnail);
        }

        if(!empty($affiliateLink)) {
            $shop->setAffiliateLink(new AffiliateLink($affiliateLink));
        }

        if(!empty($price)) {
            $shop->setPrice(new Price($price, new Currency($currency)));
        }

        if(!empty($oldPrice)) {
            $shop->setOldPrice(new Price($oldPrice, new Currency($currency)));
        }

        return $shop;
    }

    /**
     * Build the detail objects from the raw array
     *
     * @since 0.6
     * @param array $rawDetailGroup
     * @return Detail[]
     */
    protected function getDetailsFromArray(array $rawDetailGroup)
    {
        $detailGroupId = !empty($rawDetailGroup['detail_group_id']) ? intval($rawDetailGroup['detail_group_id']) : null;
        $detailGroup = $this->detailGroupRepository->findById(new DetailTemplateGroupId($detailGroupId));
        if (empty($detailGroupId) || empty($detailGroup)) {
            return array();
        }

        $details = array();
        foreach ($detailGroup->getDetails() as $detail) {
            $detailKey = $detail->getKey()->getValue();
            $detailGroupKey = $detailGroup->getKey()->getValue();
            $key =  $detailGroupKey . '_' . $detailKey;
            $title = $detail->getTitle()->getValue();
            $unit = $detail->hasUnit() ? $detail->getUnit()->getValue() : null;
            $type = $detail->getType()->getValue();
            $value = !empty($rawDetailGroup[$detailKey]) ? $rawDetailGroup[$detailKey] : null;

            $temp = new Detail(
                new Title($title),
                new Key($key),
                new DetailType($type)
            );

            if(!empty($unit)) {
                $temp->setUnit(new Unit($unit));
            }

            if(!empty($value)) {
                // Convert the string into a float, if the type is numeric
                $value = $detail->getType()->isEqualTo(DetailType::number()) ? floatval($value) : $value;

                $temp->setValue(new Value($value));
            }

            $details[] = $temp;
        }

        return $details;
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
     */
    protected function storeShops(Product $product)
    {
        /*
        $shops = array(
            'amazon' =>array(
                0 => array(
                    'shop_id' => 3,
                    'affiliate_id' => 3,
                    'affiliate_link' => 3,
                    'price' => 3,
                    'old_price' => 3,
                )
            )
        );
        */
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
        $postType = $product instanceof ProductVariant ? ProductVariant::POST_TYPE : Product::POST_TYPE;

        $args = wp_parse_args(array(
            'post_title' => $product->getTitle()->getValue(),
            'post_status' => 'publish',
            'post_name' => $product->getName()->getValue(),
            'post_type' => $postType,
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
