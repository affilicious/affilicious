<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Exception\FailedToDeleteProductException;
use Affilicious\Product\Domain\Exception\ProductNotFoundException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonProductRepository extends AbstractCarbonProductRepository implements ProductRepositoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     * @throws InvalidPostTypeException
     */
    public function store(Product $product)
    {
        // Only the post type 'product' is allowed.
        if($product instanceof ProductVariant) {
            throw new InvalidPostTypeException(ProductVariant::POST_TYPE, Product::POST_TYPE);
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
        $this->storeShops($product);
        $this->storeVariants($product);
        $this->storeReview($product);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     * @throws ProductNotFoundException
     * @throws InvalidPostTypeException
     * @throws FailedToDeleteProductException
     */
    public function delete(ProductId $productVariantId)
    {
        $post = get_post($productVariantId->getValue());
        if (empty($post)) {
            throw new ProductNotFoundException($productVariantId);
        }

        if($post->post_type != Product::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Product::POST_TYPE);
        }

        $post = wp_delete_post($productVariantId->getValue(), false);
        if(empty($post)) {
            throw new FailedToDeleteProductException($productVariantId);
        }

        $product = $this->buildProductFromPost($post);
        $product->setId(null);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function findById(ProductId $productId)
    {
        $post = get_post($productId->getValue());
        if ($post === null || $post->post_status !== 'publish') {
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

        // Title, Name
        $product = new Product(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        // ID
        $product->setId(new ProductId($post->ID));

        // Type
        $product = $this->addType($product, $post);

        // Thumbnail
        $product = $this->addThumbnail($product, $post);

        // Content
        $product = $this->addContent($product, $post);

        // Shops
        $product = $this->addShops($product);

        // Variants
        $product = $this->addVariants($product);

        // Details
        $product = $this->addDetails($product, $post);

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
        /*
        $variants = array(
            '_' => array(
                0 => array(
                    'title' => 'test',
                    'thumbnail' => '',
                    'shops' => array(
                        'amazon' =>array(
                            0 => array(
                                'shop_id' => 3,
                            )
                        )
                    )
                )
            )
        );
        */
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
}
