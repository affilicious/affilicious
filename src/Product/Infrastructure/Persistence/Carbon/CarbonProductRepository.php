<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupId;
use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Excerpt;
use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\ImageId;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Persistence\Carbon\AbstractCarbonRepository;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupId;
use Affilicious\Product\Domain\Exception\FailedToDeleteProductException;
use Affilicious\Product\Domain\Exception\MissingParentProductException;
use Affilicious\Product\Domain\Exception\ProductNotFoundException;
use Affilicious\Product\Domain\Model\AttributeGroup\AttributeGroup;
use Affilicious\Product\Domain\Model\AttributeGroup\AttributeGroupFactoryInterface;
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

class CarbonProductRepository extends AbstractCarbonRepository implements ProductRepositoryInterface
{
    const TYPE = 'affilicious_product_type';
    const IMAGE_GALLERY = '_affilicious_product_image_gallery';

    const SHOPS = 'affilicious_product_shops';
    const SHOP_TEMPLATE_ID = 'shop_template_id';
    const SHOP_PRICE = 'price';
    const SHOP_OLD_PRICE = 'old_price';
    const SHOP_CURRENCY = 'currency';
    const SHOP_AFFILIATE_ID = 'affiliate_id';
    const SHOP_AFFILIATE_LINK = 'affiliate_link';

    const DETAIL_GROUPS = 'affilicious_product_detail_groups';
    const DETAIL_TEMPLATE_GROUP_ID = 'affilicious_product_detail_template_group_id';

    const ATTRIBUTE_GROUP_KEY = 'affilicious_product_attribute_group_key';
    const ATTRIBUTE_GROUPS = 'affilicious_product_attribute_groups';
    const ATTRIBUTE_GROUP_TEMPLATE_ID = 'template_id';
    const ATTRIBUTE_GROUP_ATTRIBUTES = 'attributes';

    const VARIANTS = 'affilicious_product_variants';
    const VARIANT_TITLE = 'title';
    const VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID = 'template_group_id';
    const VARIANT_ATTRIBUTES = 'attributes';
    const VARIANT_ATTRIBUTES_CUSTOM_VALUE = 'custom_value';
    const VARIANT_THUMBNAIL = 'thumbnail';
    const VARIANT_SHOPS = 'shops';

    const REVIEW_RATING = 'affilicious_product_review_rating';
    const REVIEW_VOTES = 'affilicious_product_review_votes';

    const RELATED_PRODUCTS = 'affilicious_product_related_products';
    const RELATED_ACCESSORIES = 'affilicious_product_related_accessories';

    // TODO: Remove the legacy support in the beta
    const SHOP_ID = 'shop_id';
    const DETAIL_GROUP_ID = 'detail_group_id';

    /**
     * @var DetailGroupFactoryInterface
     */
    protected $detailGroupFactory;

    /**
     * @var AttributeGroupFactoryInterface
     */
    protected $attributeGroupFactory;

    /**
     * @var ShopTemplateRepositoryInterface
     */
    protected $shopFactory;

    /**
     * @since 0.6
     * @param DetailGroupFactoryInterface $detailGroupFactory
     * @param AttributeGroupFactoryInterface $attributeGroupFactory,
     * @param ShopFactoryInterface $shopFactory
     */
    public function __construct(
        DetailGroupFactoryInterface $detailGroupFactory,
        AttributeGroupFactoryInterface $attributeGroupFactory,
        ShopFactoryInterface $shopFactory
    )
    {
        $this->detailGroupFactory = $detailGroupFactory;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->shopFactory = $shopFactory;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function store(Product $product)
    {
        // Product variants must have a parent product
        if($product instanceof ProductVariant && !$product->getParent()->hasId()) {
            throw new MissingParentProductException($product->getId());
        }

        // Store the product into the database
        $defaultArgs = $this->getDefaultArgs($product);
        $args = $this->getArgs($product, $defaultArgs);
        $id = !empty($args['ID']) ? wp_update_post($args) : wp_insert_post($args);

        // The ID and the name might has changed. Update both values
        if(empty($defaultArgs)) {
            $post = get_post($id, OBJECT);
            $product->setId(new ProductId($post->ID));
            $product->setName(new Name($post->post_name));
        }

        // Store the product meta
        $this->storeType($product);
        $this->storeThumbnail($product);
        $this->storeShops($product, self::SHOPS);
        $this->storeReview($product);

        if($product instanceof ProductVariant){
            $this->storeAttributeGroup($product);
        }

        if(!($product instanceof ProductVariant)){
            $this->storeVariants($product);
        }

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function storeAll($products)
    {
        $storedProducts = array();
        foreach ($products as $product) {
            $storedProduct = $this->store($product);
            $storedProducts[] = $storedProduct;
        }

        return $storedProducts;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function delete(ProductId $productId)
    {
        $post = get_post($productId->getValue());
        if (empty($post)) {
            throw new ProductNotFoundException($productId);
        }

        if($post->post_type != Product::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Product::POST_TYPE);
        }

        $post = wp_delete_post($productId->getValue(), false);
        if(empty($post)) {
            throw new FailedToDeleteProductException($productId);
        }

        $product = $this->buildProductFromPost($post);
        $product->setId(null);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function deleteAll($products)
    {
        $deletedProducts = array();
        foreach ($products as $product) {
            if($product instanceof Product && $product->hasId()) {
                $deletedProduct = $this->delete($product->getId());
                $deletedProducts[] = $deletedProduct;
            }
        }

        return $deletedProducts;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function findById(ProductId $productId)
    {
        $post = get_post($productId->getValue());
        if (empty($post) || $post->post_status !== 'publish') {
            return null;
        }

        $product = self::buildProductFromPost($post);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
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
    protected function buildProductFromPost(\WP_Post $post)
    {
        if($post->post_type !== Product::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Product::POST_TYPE);
        }

        $parentPostId = wp_get_post_parent_id($post->ID);
        if(!empty($parentPostId) && $parentPostId !== 0) {
            $parentPost = get_post($parentPostId);
            $parent = $this->buildProductFromPost($parentPost);
            $productVariant = $this->buildProductVariantFromPost($post, $parent);

            return $productVariant;
        }

        // Type
        $type = carbon_get_post_meta($post->ID, self::TYPE);
        if(empty($type)) {
            //TODO: Remove the legacy support in future version
            $type = Type::SIMPLE;
            //return null;
        }

        // Title, Name
        $name = new Name($post->post_name);
        $product = new Product(
            new Title($post->post_title),
            $name,
            $name->toKey(),
            new Type($type)
        );

        // ID
        $product->setId(new ProductId($post->ID));

        // Type
        $product = $this->addType($product, $post);

        // Thumbnail
        $product = $this->addThumbnail($product, $post);

        // Content
        $product = $this->addContent($product, $post);

        // Excerpt
        $product = $this->addExcerpt($product, $post);

        // Shops
        $product = $this->addShops($product);

        // Variants
        $product = $this->addVariants($product);

        // Detail groups
        $product = $this->addDetailGroups($product, $post);

        // Review
        $product = $this->addReview($product, $post);

        // Related products
        $product = $this->addRelatedProducts($product, $post);

        // Related accessories
        $product = $this->addRelatedAccessories($product, $post);

        // Image Gallery
        $product = $this->addImageGallery($product, $post);

        return $product;
    }

    /**
     * Convert the Wordpress post into a product variant
     *
     * @since 0.6
     * @param \WP_Post $post
     * @param Product $parent
     * @return Product
     */
    protected function buildProductVariantFromPost(\WP_Post $post, Product $parent = null)
    {
        if($post->post_type !== Product::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Product::POST_TYPE);
        }

        // Find the attribute groups
        $rawAttributeGroups = carbon_get_post_meta($post->ID, self::ATTRIBUTE_GROUPS, 'complex');
        if(empty($rawAttributeGroups)) {
            return null;
        }

        // There is always just one group inside the array
        $rawAttributeGroup = $rawAttributeGroups[0];
        $rawTemplateId = $rawAttributeGroup[self::ATTRIBUTE_GROUP_TEMPLATE_ID];
        $rawAttributes = $rawAttributeGroup[self::ATTRIBUTE_GROUP_ATTRIBUTES];

        $attributeGroup = $this->getAttributeGroupFromIdAndArray($rawTemplateId, $rawAttributes);
        if ($attributeGroup === null) {
            return null;
        }

        if($parent === null) {
            $parent = $this->findParentProduct(new ProductId($post->ID));
        }

        // Parent, Title, Name, Attribute Group
        $name = new Name($post->post_name);
        $productVariant = new ProductVariant(
            $parent,
            new Title($post->post_title),
            $name,
            $name->toKey(),
            $attributeGroup
        );

        // ID
        $productVariant->setId(new ProductId($post->ID));

        // Thumbnail
        $productVariant = $this->addThumbnail($productVariant, $post);

        // Shops
        $productVariant = $this->addShops($productVariant);

        return $productVariant;
    }

    /**
     * Find the parent of the product variant by the given ID
     *
     * @since 0.6
     * @param ProductId $productVariantId
     * @return Product
     */
    protected function findParentProduct(ProductId $productVariantId)
    {
        $parentPostId = wp_get_post_parent_id($productVariantId->getValue());
        if(empty($parentPostId)) {
            //throw new MissingParentProductException($productVariantId);
            return null;
        }

        $parent = $this->findById($productVariantId);

        return $parent;
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
     * Add the excerpt to the product
     *
     * @since 0.6
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    protected function addExcerpt(Product $product, \WP_Post $post)
    {
        $excerpt = $post->post_excerpt;
        if(!empty($excerpt)) {
            $product->setExcerpt(new Excerpt($excerpt));
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
     * Add the variants to the product
     *
     * @since 0.6
     * @param Product $product
     * @return Product
     */
    protected function addVariants(Product $product)
    {
        $rawVariants = carbon_get_post_meta($product->getId()->getValue(), self::VARIANTS, 'complex');

        foreach ($rawVariants as $rawVariant)
        {
            $title = !empty($rawVariant[self::VARIANT_TITLE]) ? $rawVariant[self::VARIANT_TITLE] : null;
            $thumbnailId = !empty($rawVariant[self::VARIANT_THUMBNAIL]) ? $rawVariant[self::VARIANT_THUMBNAIL] : null;
            $shops = !empty($rawVariant[self::VARIANT_SHOPS]) ? $rawVariant[self::VARIANT_SHOPS] : null;
            $attributeTemplateGroupId = !empty($rawVariant[self::VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID]) ? $rawVariant[self::VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID] : null;
            $attributes = !empty($rawVariant[self::VARIANT_ATTRIBUTES]) ? $rawVariant[self::VARIANT_ATTRIBUTES] : null;
            $attributeGroup = $this->getAttributeGroupFromIdAndArray($attributeTemplateGroupId, $attributes);

            if(empty($title) || empty($attributeGroup)) {
                continue;
            }

            $title = new Title($title);
            $name = $title->toName();
            $key = $name->toKey();
            $productVariant = new ProductVariant(
                $product,
                $title,
                $name,
                $key,
                $attributeGroup
            );

            $thumbnail = $this->getImageFromAttachmentId($thumbnailId);
            if(!empty($thumbnail)) {
                $productVariant->setThumbnail($thumbnail);
            }

            if(!empty($shops)) {
                $this->addShops($productVariant, $shops);
            }

            $product->addVariant($productVariant);
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
     * Build the attribute group from the raw array
     *
     * @since 0.6
     * @param int $attributeTemplateGroupId
     * @param array $rawAttributeGroup
     * @return AttributeGroup|null
     */
    protected function getAttributeGroupFromIdAndArray($attributeTemplateGroupId, array $rawAttributeGroup)
    {
        if (empty($attributeTemplateGroupId) || empty($rawAttributeGroup)) {
            return null;
        }

        $attributeGroup = $this->attributeGroupFactory->createFromTemplateIdAndData(
            new AttributeTemplateGroupId($attributeTemplateGroupId),
            $rawAttributeGroup
        );

        return $attributeGroup;
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
        foreach ($shops as $index => $shop) {
            if(!isset($carbonShops[$shop->getKey()->getValue()])) {
                $carbonShops[$shop->getKey()->getValue()] = array();
            }

            $carbonShops[$shop->getKey()->getValue()][$index] = array(
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
     * Store the attribute group of the product
     *
     * @since 0.6
     * @param ProductVariant $productVariant
     */
    protected function storeAttributeGroup(ProductVariant $productVariant)
    {
        $attributeGroup = $productVariant->getAttributeGroup();
        $attributes = $attributeGroup->getAttributes();

        $carbonAttributes = array();
        foreach ($attributes as $index => $attribute) {
            if(!isset($carbonShops[$attribute->getKey()->getValue()])) {
                $carbonShops[$attribute->getKey()->getValue()] = array();
            }

            $carbonAttributes[$attribute->getKey()->getValue()][$index] = array(
                self::VARIANT_ATTRIBUTES_CUSTOM_VALUE => $attribute->getValue()->getValue(),
            );
        }

        $carbonAttributeGroups = array();
        $carbonAttributeGroups[$attributeGroup->getKey()->getValue()][] = array(
            self::ATTRIBUTE_GROUP_TEMPLATE_ID => $attributeGroup->hasTemplateId() ? $attributeGroup->getTemplateId()->getValue() : null,
            self::ATTRIBUTE_GROUP_ATTRIBUTES => $carbonAttributes,
        );

        $carbonMetaKeys = $this->buildComplexCarbonMetaKey($carbonAttributeGroups, self::ATTRIBUTE_GROUPS);
        foreach ($carbonMetaKeys as $carbonMetaKey => $carbonMetaValue) {
            if($carbonMetaValue !== null && $productVariant->hasId()) {
                $this->storePostMeta($productVariant->getId(), $carbonMetaKey, $carbonMetaValue);
            }
        }
    }

    /**
     * Store the variants for the product
     *
     * @since 0.6
     * @param Product $product
     */
    protected function storeVariants(Product $product)
    {
        $variants = $product->getVariants();
        if(empty($variant)) {
            return;
        }

        /* Example for valid structure:
         *
         * $variants = array(
         *     '_' => array(
         *         0 => array(
         *             'title' => 'test',
         *             'thumbnail' => '',
         *             'shops' => array(
         *                 'amazon' => array(
         *                     0 => array(
         *                        'shop_template_id' => 1234,
         *                        'affiliateLink' => 'http://your-link.com',
         *                        'currency' => 'euro',
         *                        ...
         *                     )
         *                 )
         *             )
         *         ),
         *         ...
         *     )
         * );
         */
        $carbonVariants = array('_' => array());
        foreach ($variants as $variant) {

            $shops = $variant->getShops();
            $carbonShops = array();
            foreach ($shops as $shop) {
                if(!isset($carbonShops[$shop->getKey()->getValue()])) {
                    $carbonShops[$shop->getKey()->getValue()] = array();
                }

                $carbonShops[$shop->getKey()->getValue()][] = array(
                    self::SHOP_TEMPLATE_ID => $shop->hasTemplateId() ? $shop->getTemplateId()->getValue() : null,
                    self::SHOP_AFFILIATE_ID => $shop->hasAffiliateId() ? $shop->getAffiliateId()->getValue() : null,
                    self::SHOP_AFFILIATE_LINK => $shop->getAffiliateLink()->getValue(),
                    self::SHOP_CURRENCY => $shop->getCurrency()->getValue(),
                    self::SHOP_PRICE => $shop->hasPrice() ? $shop->getPrice()->getValue() : null,
                    self::SHOP_OLD_PRICE => $shop->hasOldPrice() ? $shop->getOldPrice()->getValue() : null,
                );
            }

            $carbonVariants['_'][] = array(
                'title' => $variant->getTitle()->getValue(),
                'thumbnail' => $variant->getThumbnail()->getId()->getValue(),
                'shops' => !empty($carbonShops) ? $carbonShops : null,
            );
        }


        $carbonMetaKeys = $this->buildComplexCarbonMetaKey($carbonVariants, self::VARIANTS);
        foreach ($carbonMetaKeys as $carbonMetaKey => $carbonMetaValue) {
            if($carbonMetaValue !== null && $product->hasId()) {
                $this->storePostMeta($product->getId(), $carbonMetaKey, $carbonMetaValue);
            }
        }
    }

    /**
     * Store the review for the product
     *
     * @since 0.6
     * @param Product $product
     */
    protected function storeReview(Product $product)
    {
        if($product->hasReview()) {
            $this->storePostMeta($product->getId(), self::REVIEW_RATING, $product->getReview()->getRating());

            if($product->getReview()->hasVotes()) {
                $this->storePostMeta($product->getId(), self::REVIEW_VOTES, $product->getReview()->getVotes());
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
     * @inheritdoc
     */
    public function deleteAllVariantsFromParentExcept($productVariants, ProductId $parentProductId)
    {
        $notToDelete = array();
        foreach ($productVariants as $productVariant) {
            if(!($productVariant instanceof ProductVariant)) {
                throw new InvalidTypeException($productVariant, 'Affilicious\Product\Domain\Model\Variant\ProductVariant');
            }

            if(!$productVariant->getParent()->hasId() || !$productVariant->hasId()) {
                continue;
            }

            if(!$parentProductId->isEqualTo($productVariant->getParent()->getId())) {
                continue;
            }

            $notToDelete[] = $productVariant->getId()->getValue();
        }

        $toDelete = array();
        foreach ($productVariants as $productVariant) {
            if($productVariant instanceof ProductVariant) {

                $parentId = $productVariant->getParent()->getId()->getValue();
                if(!isset($toDelete[$parentId])) {
                    $toDelete[$parentId] = array();
                }

                $toDelete[$parentId][] = $productVariant->getId()->getValue();
            }
        }

        $query = new \WP_Query(array(
            'post_type' => Product::POST_TYPE,
            'post_parent' => $parentProductId->getValue(),
            'post__not_in' => $notToDelete,
        ));

        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_post($query->post->ID, true);
            }

            wp_reset_postdata();
        }
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

        if($product->hasExcerpt()) {
            $args['post_excerpt'] = $product->getExcerpt()->getValue();
        }

        if($product instanceof ProductVariant) {
            $args['post_parent'] = $product->getParent()->getId()->getValue();
        }

        return $args;
    }
}
