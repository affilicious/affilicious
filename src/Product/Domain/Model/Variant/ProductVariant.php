<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Product\Domain\Model\Detail\Detail;
use Affilicious\Product\Domain\Model\Detail\Key;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductVariant extends Product
{
    const POST_TYPE = 'product_variant';
    const SLUG = 'product-variant';

    /**
     * @var Product
     */
    protected $parent;

    /**
     * @param ProductId $id
     * @param Product $parent
     * @param Title $title
     */
    public function __construct(ProductId $id, Product $parent, Title $title)
    {
        parent::__construct($id, $parent->getType(), $title);
        $this->parent = $parent;
    }

    /**
     * @return Product
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasDetail(Key $key)
    {
        return $this->parent->hasDetail($key);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function addDetail(Detail $detail)
    {
        $this->parent->addDetail($detail);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function removeDetail(Key $key)
    {
        $this->parent->removeDetail($key);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getDetail(Key $key)
    {
        return $this->getDetail($key);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getDetails()
    {
        return $this->parent->getDetails();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasReview()
    {
        return $this->parent->hasReview();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getReview()
    {
        return $this->parent->getReview();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setReview(Review $review)
    {
        $this->parent->setReview($review);
    }

    /**
     *  @inheritdoc
     * @since 0.6
     */
    public function getRelatedProducts()
    {
        return $this->parent->getRelatedProducts();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setRelatedProducts($relatedProducts)
    {
        $this->parent->setRelatedProducts($relatedProducts);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getRelatedAccessories()
    {
        return $this->parent->getRelatedAccessories();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setRelatedAccessories($relatedAccessories)
    {
        return $this->parent->setRelatedProducts($relatedAccessories);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            parent::isEqualTo($object) &&
            $this->getParent()->isEqualTo($object->getParent());
    }
}
