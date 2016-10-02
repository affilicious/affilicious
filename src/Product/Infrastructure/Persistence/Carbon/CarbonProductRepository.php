<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Detail\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\Product\Domain\Exception\FailedToDeleteProductException;
use Affilicious\Product\Domain\Exception\ProductVariantNotFoundException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;
use Affilicious\Product\Domain\Model\Shop\ShopFactoryInterface;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Affilicious\Product\Domain\Model\Variant\ProductVariantRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonProductRepository extends AbstractCarbonProductRepository implements ProductRepositoryInterface
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    protected $productVariantRepository;

    /**
     * @since 0.6
     * @param ProductVariantRepositoryInterface $productVariantRepository
     * @param DetailGroupRepositoryInterface $detailGroupRepository
     * @param ShopFactoryInterface $shopFactory
     */
    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        DetailGroupRepositoryInterface $detailGroupRepository,
        ShopFactoryInterface $shopFactory
    )
    {
        $this->productVariantRepository = $productVariantRepository;
        parent::__construct($detailGroupRepository, $shopFactory);
    }

    /**
     * @inheritdoc
     * @since 0.6
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
        $id = wp_insert_post($args);

        // The ID and the name might has changed. Update both values
        if(empty($defaultArgs)) {
            $post = get_post($id, OBJECT);
            $product->setId(new ProductId($post->ID));
            $product->setName(new Name($post->post_name));
        }

        // Store the product meta
        $this->storePostMeta($id, self::TYPE, $product->getType());
        $this->storeVariants($product);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     * @throws ProductVariantNotFoundException
     * @throws InvalidPostTypeException
     * @throws FailedToDeleteProductException
     */
    public function delete(ProductId $productVariantId)
    {
        $post = get_post($productVariantId->getValue());
        if ($post === null) {
            throw new ProductVariantNotFoundException($productVariantId);
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
        if ($post === null) {
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
     * Store the variants for the product
     *
     * @since 0.6
     * @param Product $product
     */
    protected function storeVariants(Product $product)
    {

    }
}
