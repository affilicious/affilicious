<?php
use Affilicious\ProductsPlugin\Product\Field\DetailGroup;
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
        $detailGroup = new DetailGroup('key', 'text', 'label');

        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());

        $product->addDetailGroup($detailGroup);
        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());

        $product->removeDetailGroup($detailGroup->getId());
        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());
    }

    /**
     * @covers Product::addDetailGroup
     */
    public function testAddDetailGroup()
    {
        $product = $this->createProduct();
        $detailGroup = new DetailGroup('key', 'text', 'label');

        $this->assertCount(0, $product->getDetailGroups());

        $product->addDetailGroup($detailGroup);
        $this->assertCount(1, $product->getDetailGroups());
    }

    /**
     * @covers Product::removeDetailGroup
     * @depends testAddDetailGroup
     */
    public function testRemoveDetailGroup()
    {
        $product = $this->createProduct();;
        $detailGroup = new DetailGroup('key', 'text', 'label');
        $product->addDetailGroup($detailGroup);

        $this->assertCount(1, $product->getDetailGroups());

        $product->removeDetailGroup($detailGroup->getId());
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
        $detailGroup = new DetailGroup('key', 'text', 'label');

        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());

        $product->addDetailGroup($detailGroup);
        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());

        $product->removeDetailGroup($detailGroup->getId());
        $this->assertCount($product->countDetailGroups(), $product->getDetailGroups());
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
