<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\ImageId;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Detail\Domain\Model\DetailGroupId;
use Affilicious\Detail\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\Product\Domain\Model\Detail\Detail;
use Affilicious\Product\Domain\Model\Detail\Key;
use Affilicious\Product\Domain\Model\Detail\Name;
use Affilicious\Product\Domain\Model\Detail\Type;
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
use Affilicious\Product\Domain\Model\Shop\Logo;
use Affilicious\Product\Domain\Model\Shop\Price;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Shop\ShopId;
use Affilicious\Product\Domain\Model\Content as ProductContent;
use Affilicious\Product\Domain\Model\Shop\Title as ShopTitle;
use Affilicious\Product\Domain\Model\Title as ProductTitle;
use Affilicious\Product\Domain\Model\Type as ProductType;
use Affilicious\Detail\Domain\Model\Detail\Type as DetailType;
use Affilicious\Shop\Domain\Model\ShopRepositoryInterface;
use Affilicious\Shop\Domain\Model\ShopId as ShopTemplateId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonProductRepository implements ProductRepositoryInterface
{
    const PRODUCT_TYPE = 'affilicious_product_type';

    const PRODUCT_SHOPS_ENABLED = 'affilicious_product_shops_enabled';
    const PRODUCT_SHOPS = 'affilicious_product_shops';

    const PRODUCT_DETAIL_GROUPS_ENABLED = 'affilicious_product_detail_groups_enabled';
    const PRODUCT_DETAIL_GROUPS = 'affilicious_product_detail_groups';

    const PRODUCT_REVIEW_ENABLED = 'affilicious_product_review_enabled';
    const PRODUCT_REVIEW_RATING = 'affilicious_product_review_rating';
    const PRODUCT_REVIEW_VOTES = 'affilicious_product_review_votes';

    const PRODUCT_RELATED_PRODUCTS = 'affilicious_product_related_products';
    const PRODUCT_RELATED_ACCESSORIES = 'affilicious_product_related_accessories';
    const PRODUCT_IMAGE_GALLERY = '_affilicious_product_image_gallery';

    /**
     * @var DetailGroupRepositoryInterface
     */
    private $detailGroupRepository;

    /**
     * @var ShopRepositoryInterface
     */
    private $shopRepository;

    /**
     * @since 0.5.2
     * @param DetailGroupRepositoryInterface $detailGroupRepository
     * @param ShopRepositoryInterface $shopRepository
     */
    public function __construct(
        DetailGroupRepositoryInterface $detailGroupRepository,
        ShopRepositoryInterface $shopRepository
    )
    {
        $this->detailGroupRepository = $detailGroupRepository;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @inheritdoc
     */
    public function findById(ProductId $productId)
    {
        $post = get_post($productId->getValue());
        if ($post === null) {
            return null;
        }

        $product = self::buildProductFromPost($post);
        return $product;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => Product::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $products = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = self::buildProductFromPost($query->post);
                $products[] = $product;
            }

            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Convert the Wordpress post into a product
     *
     * @since 0.3
     * @param \WP_Post $post
     * @return Product
     */
    private function buildProductFromPost(\WP_Post $post)
    {
        // Type
        $type = carbon_get_post_meta($post->ID, self::PRODUCT_TYPE);
        $type = empty($type) ? ProductType::SIMPLE : $type;

        // ID, Title
        $product = new Product(
            new ProductId($post->ID),
            new ProductType($type),
            new ProductTitle($post->post_title)
        );

        // Thumbnail
        $thumbnailId = get_post_thumbnail_id($post->ID);
        if (!empty($thumbnailId)) {
            $thumbnail = self::buildImageFromAttachmentId($thumbnailId);

            if($thumbnail !== null) {
                $product->setThumbnail($thumbnail);
            }
        }

        // Content
        $content = $post->post_content;
        if(!empty($content)) {
            $product->setContent(new ProductContent($content));
        }

        // Shops
        $enabledShops = carbon_get_post_meta($post->ID, self::PRODUCT_SHOPS_ENABLED);
        if(!empty($enabledShops) && $enabledShops === 'yes') {
            $shops = carbon_get_post_meta($post->ID, self::PRODUCT_SHOPS, 'complex');
            if (!empty($shops)) {
                foreach ($shops as $shop) {
                    $shop = self::buildShopFromArray($shop);

                    if ($shop !== null) {
                        $product->addShop($shop);
                    }
                }
            }
        }

        // Details
        $enabledDetailGroups = carbon_get_post_meta($post->ID, self::PRODUCT_DETAIL_GROUPS_ENABLED);
        if(!empty($enabledDetailGroups) && $enabledDetailGroups === 'yes') {
            $detailGroups = carbon_get_post_meta($post->ID, self::PRODUCT_DETAIL_GROUPS, 'complex');
            if (!empty($detailGroups)) {
                foreach ($detailGroups as $detailGroup) {
                    $details = self::buildDetailsFromArray($detailGroup);

                    if (!empty($details)) {
                        $product->setDetails($details);
                    }
                }
            }
        }

        // Review
        $enabledReview = carbon_get_post_meta($post->ID, self::PRODUCT_REVIEW_ENABLED);
        if(!empty($enabledReview) && $enabledReview === 'yes') {
            $rating = carbon_get_post_meta($post->ID, self::PRODUCT_REVIEW_RATING);
            $votes = carbon_get_post_meta($post->ID, self::PRODUCT_REVIEW_VOTES);

            $review = new Review(new Rating($rating));
            if(!empty($votes)) {
                $review->setVotes(new Votes($votes));
            }

            $product->setReview($review);
        }

        // Related products
        $relatedProducts = carbon_get_post_meta($post->ID, self::PRODUCT_RELATED_PRODUCTS);
        if (!empty($relatedProducts)) {
            $relatedProducts = array_map(function ($value) {
                return new ProductId(intval($value));
            }, $relatedProducts);

            $product->setRelatedProducts($relatedProducts);
        }

        // Related accessories
        $relatedAccessories = carbon_get_post_meta($post->ID, self::PRODUCT_RELATED_ACCESSORIES);
        if (!empty($relatedAccessories)) {
            $relatedAccessories = array_map(function ($value) {
                return new ProductId(intval($value));
            }, $relatedAccessories);

            $product->setRelatedAccessories($relatedAccessories);
        }

        // Image gallery
        $imageGallery = get_post_meta($post->ID, self::PRODUCT_IMAGE_GALLERY);
        if (!empty($imageGallery)) {
            $imageIds = explode(',', $imageGallery[0]);
            $imageIds = array_map(function ($value) {
                return intval($value);
            }, $imageIds);

            $images = array();
            foreach ($imageIds as $imageId) {
                $image = self::buildImageFromAttachmentId($imageId);

                if($image !== null) {
                    $images[] = $image;
                }
            }

            $product->setImageGallery($images);
        }

        return $product;
    }

    /**
     * @since 0.5.2
     * @param array $rawShop
     * @return null|Shop
     */
    private function buildShopFromArray(array $rawShop)
    {
        $shopId = !empty($rawShop['shop_id']) ? intval($rawShop['shop_id']) : null;
        $shopTemplate = $this->shopRepository->findById(new ShopTemplateId($shopId));

        if (empty($shopId) || empty($shopTemplate)) {
            return null;
        }

        $title = $shopTemplate->getTitle()->getValue();
        $logo = $shopTemplate->getLogo();
        $price = !empty($rawShop['price']) ? floatval($rawShop['price']) : null;
        $oldPrice = !empty($rawShop['old_price']) ? floatval($rawShop['old_price']) : null;
        $currency = !empty($rawShop['currency']) ? $rawShop['currency'] : null;
        $affiliateId = !empty($rawShop['affiliate_id']) ? $rawShop['affiliate_id'] : null;
        $affiliateLink = !empty($rawShop['affiliate_link']) ? $rawShop['affiliate_link'] : null;

        $shop = new Shop(
            new ShopId($shopId),
            new ShopTitle($title),
            new Currency($currency)
        );

        if($shopTemplate->hasLogo()) {
            $shop->setLogo(new Logo($logo->getValue()));
        }

        if(!empty($price)) {
            $shop->setPrice(new Price($price, new Currency($currency)));
        }

        if(!empty($oldPrice)) {
            $shop->setOldPrice(new Price($oldPrice, new Currency($currency)));
        }

        if(!empty($affiliateId)) {
            $shop->setAffiliateId(new AffiliateId($affiliateId));
        }

        if(!empty($affiliateLink)) {
            $shop->setAffiliateLink(new AffiliateLink($affiliateLink));
        }

        return $shop;
    }

    /**
     * @since 0.5.2
     * @param array $rawDetailGroup
     * @return Detail[]
     */
    private function buildDetailsFromArray(array $rawDetailGroup)
    {
        $detailGroupId = !empty($rawDetailGroup['detail_group_id']) ? intval($rawDetailGroup['detail_group_id']) : null;
        $detailGroup = $this->detailGroupRepository->findById(new DetailGroupId($detailGroupId));

        if (empty($detailGroupId) || empty($detailGroup)) {
            return array();
        }

        $details = array();

        foreach ($detailGroup->getDetails() as $detail) {
            $detailKey = $detail->getKey()->getValue();
            $detailGroupKey = $detailGroup->getKey()->getValue();
            $key =  $detailGroupKey . '_' . $detailKey;
            $name = $detail->getName()->getValue();
            $unit = $detail->hasUnit() ? $detail->getUnit()->getValue() : null;
            $type = $detail->getType()->getValue();
            $value = !empty($rawDetailGroup[$detailKey]) ? $rawDetailGroup[$detailKey] : null;

            $temp = new Detail(
                new Key($key),
                new Type($type),
                new Name($name)
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
     * @since 0.5.2
     * @param int $attachmentId
     * @return null|Image
     */
    private function buildImageFromAttachmentId($attachmentId)
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
}
