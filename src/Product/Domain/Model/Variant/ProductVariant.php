<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\DetailGroup\DetailGroup;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductVariant extends Product
{
    /**
     * There is a limit of 20 characters for post types in Wordpress
     */
    const POST_TYPE = 'aff_product_variant';

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
        parent::__construct($title, $name, $parent->type);
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
    public function hasDetailGroup(Name $name)
    {
        return $this->parent->hasDetailGroup($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function addDetailGroup(DetailGroup $detailGroup)
    {
        $this->parent->addDetailGroup($detailGroup);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function removeDetailGroup(Name $name)
    {
        $this->parent->removeDetailGroup($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getDetailGroup(Name $name)
    {
        return $this->getDetailGroup($name);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getDetailGroups()
    {
        return $this->parent->getDetailGroups();
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
    public function setReview($review)
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
            $this->getName()->isEqualTo($object->getName());
    }
}
