<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\AttributeGroup\AttributeGroup;
use Affilicious\Product\Domain\Model\DetailGroup\DetailGroup;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductVariant extends Product
{
    /**
     * @var Product
     */
    protected $parent;

    /**
     * @var AttributeGroup
     */
    protected $attributeGroup;

    /**
     * @since 0.6
     * @param Product $parent
     * @param Title $title
     * @param Name $name
     * @param AttributeGroup $attributeGroup
     */
    public function __construct(Product $parent, Title $title, Name $name, AttributeGroup $attributeGroup)
    {
        parent::__construct($title, $name, $parent->type);
        $this->parent = $parent;
        $this->attributeGroup = $attributeGroup;
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
    public function setContent($content)
    {
        $this->parent->setContent($content);
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function hasExcerpt()
    {
        return $this->parent->hasExcerpt();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function getExcerpt()
    {
        return $this->parent->getExcerpt();
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function setExcerpt($excerpt)
    {
        $this->parent->setExcerpt($excerpt);
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
     * Get the attribute groups which stores the attributes like color or size.
     *
     * @since 0.6
     * @return AttributeGroup
     */
    public function getAttributeGroup()
    {
        return $this->attributeGroup;
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
