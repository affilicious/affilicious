<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Exception\FailedToDeleteProductException;
use Affilicious\Product\Domain\Exception\MissingParentProductException;
use Affilicious\Product\Domain\Exception\ProductNotFoundException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonProductRepository extends AbstractCarbonProductRepository implements ProductRepositoryInterface
{
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
     * @param Product $parent
     * @return Product
     */
    protected function buildProductFromPost(\WP_Post $post, Product $parent = null)
    {
        if($post->post_type !== Product::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Product::POST_TYPE);
        }

        // Parent
        if($parent === null) {
            $parentPostId = wp_get_post_parent_id($post->ID);
            if(!empty($parentPostId)) {
                $parent = $this->findById(new ProductId($parentPostId));
            }
        }

        // Title, Name
        if($parent === null) {
            $product = new Product(
                new Title($post->post_title),
                new Name($post->post_name),
                Type::simple()
            );
        } else {
            $product = new ProductVariant(
                $parent,
                new Title($post->post_title),
                new Name($post->post_name)
            );
        }

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
     * Add the variants to the product
     *
     * @since 0.6
     * @param Product $product
     * @return Product
     */
    protected function addVariants(Product $product)
    {
        $rawVariants = carbon_get_post_meta($product->getId()->getValue(), self::VARIANTS, 'complex');
        if(empty($rawVariants)) {
            return $product;
        }

        foreach ($rawVariants as $rawVariant)
        {
            $title = !empty($rawVariant[self::VARIANT_TITLE]) ? $rawVariant[self::VARIANT_TITLE] : null;
            $thumbnailId = !empty($rawVariant[self::VARIANT_THUMBNAIL]) ? $rawVariant[self::VARIANT_THUMBNAIL] : null;
            $shops = !empty($rawVariant[self::VARIANT_SHOPS]) ? $rawVariant[self::VARIANT_SHOPS] : null;

            if(empty($title)) {
                continue;
            }

            $title = new Title($title);
            $productVariant = new ProductVariant(
                $product,
                $title,
                $title->toName()
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
}
