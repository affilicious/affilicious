<?php
use Affilicious\ProductsPlugin\Product\Field\FieldGroup;
use Affilicious\ProductsPlugin\Product\Detail\DetailGroup;
use Affilicious\ProductsPlugin\Product\Product;

class TestProduct extends WP_UnitTestCase
{
    /**
     * @covers Product::addDetailGroup
     */
    public function testAddDetailGroup()
    {
        $product = $this->createProduct();
        $detail = new DetailGroup('key', 'text', 'label');

        $this->assertCount(0, $product->getDetailGroups());

        $product->addDetailGroup($detail);
        $this->assertCount(1, $product->getDetailGroups());
    }

    /**
     * @covers Product::removeDetailGroup
     * @depends testAddDetailGroup
     */
    public function testRemoveDetailGroup()
    {
        $product = $this->createProduct();;
        $detail = new DetailGroup('key', 'text', 'label');
        $product->addDetailGroup($detail);

        $this->assertCount(1, $product->getDetailGroups());

        $product->removeDetailGroup($detail->getId());
        $this->assertCount(0, $product->getDetailGroups());
    }

    /**
     * @covers Product::hasDetailGroup
     * @depends testAddDetailGroup
     * @depends testRemoveDetailGroup
     */
    public function testHasDetailGroup()
    {
        $product = $this->createProduct();
        $detailGroup = new DetailGroup('key', 'text', 'label');

        $product->addDetailGroup($detailGroup);
        $this->assertTrue($product->hasDetailGroup($detailGroup->getId()));

        $product->removeDetailGroup($detailGroup->getId());
        $this->assertFalse($product->hasDetailGroup($detailGroup->getId()));
    }

    /**
     * @covers Product::getDetailGroup
     * @depends testAddDetailGroup
     * @depends testRemoveDetailGroup
     */
    public function testGetDetailGroup()
    {
        $product = $this->createProduct();
        $detailGroup = new DetailGroup('key', 'text', 'label');

        $this->assertNull($product->getDetailGroup($detailGroup->getId()));

        $product->addDetailGroup($detailGroup);
        $this->assertNotNull($product->getDetailGroup($detailGroup->getId()));

        $product->removeDetailGroup($detailGroup->getId());
        $this->assertNull($product->getDetailGroup($detailGroup->getId()));
    }

    /**
     * @covers Product::countDetailGroups
     * @depends testAddDetailGroup
     * @depends testRemoveDetailGroup
     */
    public function testCountDetailGroups()
    {
        $product = $this->createProduct();
        $fieldGroup = new DetailGroup('key', 'text', 'label');

        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());

        $product->addDetailGroup($fieldGroup);
        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());

        $product->removeDetailGroup($fieldGroup->getId());
        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());
    }

    /**
     * @covers Product::addFieldGroup
     */
    public function testAddFieldGroup()
    {
        $product = $this->createProduct();
        $fieldGroup = new FieldGroup('key', 'text', 'label');

        $this->assertCount(0, $product->getDetailGroups());

        $product->addFieldGroup($fieldGroup);
        $this->assertCount(1, $product->getFieldGroups());
    }

    /**
     * @covers Product::removeFieldGroup
     * @depends testAddFieldGroup
     */
    public function testRemoveFieldGroup()
    {
        $product = $this->createProduct();;
        $fieldGroup = new FieldGroup('key', 'text', 'label');
        $product->addFieldGroup($fieldGroup);

        $this->assertCount(1, $product->getFieldGroups());

        $product->removeFieldGroup($fieldGroup->getId());
        $this->assertCount(0, $product->getFieldGroups());
    }

    /**
     * @covers Product::hasFieldGroup
     * @depends testAddFieldGroup
     * @depends testRemoveFieldGroup
     */
    public function testHasFieldGroup()
    {
        $product = $this->createProduct();
        $fieldGroup = new FieldGroup('key', 'text', 'label');

        $product->addFieldGroup($fieldGroup);
        $this->assertTrue($product->hasFieldGroup($fieldGroup->getId()));

        $product->removeFieldGroup($fieldGroup->getId());
        $this->assertFalse($product->hasFieldGroup($fieldGroup->getId()));
    }

    /**
     * @covers Product::getFieldGroup
     * @depends testAddFieldGroup
     * @depends testRemoveFieldGroup
     */
    public function testGetFieldGroup()
    {
        $product = $this->createProduct();
        $fieldGroup = new FieldGroup('key', 'text', 'label');

        $this->assertNull($product->getFieldGroup($fieldGroup->getId()));

        $product->addFieldGroup($fieldGroup);
        $this->assertNotNull($product->getFieldGroup($fieldGroup->getId()));

        $product->removeFieldGroup($fieldGroup->getId());
        $this->assertNull($product->getFieldGroup($fieldGroup->getId()));
    }

    /**
     * @covers Product::countFieldGroups
     * @depends testAddFieldGroup
     * @depends testRemoveFieldGroup
     */
    public function testCountFieldGroups()
    {
        $product = $this->createProduct();
        $fieldGroup = new FieldGroup('key', 'text', 'label');

        $this->assertCount($product->countFieldGroups(), $product->getFieldGroups());

        $product->addFieldGroup($fieldGroup);
        $this->assertCount($product->countFieldGroups(), $product->getFieldGroups());

        $product->removeFieldGroup($fieldGroup->getId());
        $this->assertCount($product->countFieldGroups(), $product->getFieldGroups());
    }

    /**
     * @return Product
     */
    private function createProduct()
    {
        $postId = $this->factory->post->create();
        $post = get_post($postId);
        $product = new Product($post);

        return $product;
    }
}
