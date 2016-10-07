<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Product\Domain\Exception\FailedToDeleteProductException;
use Affilicious\Product\Domain\Exception\FailedToDeleteProductVariantException;
use Affilicious\Product\Domain\Exception\MissingParentProductException;
use Affilicious\Product\Domain\Exception\ProductNotFoundException;
use Affilicious\Product\Domain\Exception\ProductVariantNotFoundException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Affilicious\Product\Domain\Model\Variant\ProductVariantRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonProductVariantRepository extends AbstractCarbonProductRepository implements ProductVariantRepositoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     * @throws MissingParentProductException
     */
    public function store(Product $productVariant)
    {
        if(!($productVariant instanceof ProductVariant)) {
            throw new InvalidPostTypeException(Product::POST_TYPE, ProductVariant::POST_TYPE);
        }

        // The parent product have to be stored already
        if(!$productVariant->getParent()->hasId()) {
            throw new MissingParentProductException($productVariant->getId());
        }

        // Store the product variant into the database
        $defaultArgs = $this->getDefaultArgs($productVariant);
        $args = $this->getArgs($productVariant, $defaultArgs);
        $id = wp_insert_post($args);

        // The ID and the name might has changed. Update both values
        if(empty($defaultArgs)) {
            $post = get_post($id, OBJECT);
            $productVariant->setId(new ProductId($post->ID));
            $productVariant->setName(new Name($post->post_name));
        }

        return $productVariant;
    }

    /**
     * @inheritdoc
     * @since 0.6
     * @throws ProductVariantNotFoundException
     * @throws InvalidPostTypeException
     * @throws FailedToDeleteProductVariantException
     */
    public function delete(ProductId $productVariantId)
    {
        $post = get_post($productVariantId->getValue());
        if (empty($post)) {
            throw new ProductNotFoundException($productVariantId);
        }

        $validPostTypes = array(Product::POST_TYPE, ProductVariant::POST_TYPE);
        if(in_array($post->post_type, $validPostTypes)) {
            throw new InvalidPostTypeException($post->post_type, $validPostTypes);
        }

        $post = wp_delete_post($productVariantId->getValue(), false);
        if(empty($post)) {
            throw new FailedToDeleteProductException($productVariantId);
        }

        $product = $this->buildProductVariantFromPost($post);
        $product->setId(null);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function findById(ProductId $productVariantId)
    {
        $post = get_post($productVariantId->getValue());
        if ($post === null) {
            return null;
        }

        $productVariant = self::buildProductVariantFromPost($post);
        return $productVariant;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => ProductVariant::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $productVariants = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = self::buildProductVariantFromPost($query->post);
                $productVariants[] = $product;
            }

            wp_reset_postdata();
        }

        return $productVariants;
    }
}
