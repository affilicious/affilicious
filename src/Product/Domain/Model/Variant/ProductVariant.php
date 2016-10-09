<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Detail\Detail;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Type;

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
     * @since 0.6
     * @param Product $parent
     * @param Title $title
     * @param Name $name
     */
    public function __construct(Product $parent, Title $title, Name $name)
    {
        parent::__construct($title, $name);
        $this->parent = $parent;
    }

    /**
     * Get the parent product
     *
     * @since 0.6
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
    public function setType(Type $type)
    {
        return $this->parent->setType($type);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getType()
    {
        return $this->parent->getType();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasContent()
    {
        return $this->parent->hasContent();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getContent()
    {
        return $this->parent->getContent();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setContent(Content $content)
    {
        $this->parent->setContent($content);
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
     * Get the raw Wordpress post
     *
     * @since 0.6
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        if(!$this->hasId()) {
            return null;
        }

        return get_post($this->id->getValue());
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
