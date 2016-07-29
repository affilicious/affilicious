<?php
use Affilicious\ProductsPlugin\Product\Detail\DetailGroup;
use Affilicious\ProductsPlugin\Product\Detail\Detail;

class TestDetailGroup extends WP_UnitTestCase
{
    /**
     * @covers DetailGroup::addDetail
     */
    public function testAddDetail()
    {
        $detail = new Detail('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $this->assertCount(0, $detailGroup->getDetails());

        $detailGroup->addDetail($detail);
        $this->assertCount(1, $detailGroup->getDetails());
    }

    /**
     * @covers DetailGroup::removeDetail
     * @depends testAddDetail
     */
    public function testRemoveDetail()
    {
        $detail = new Detail('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');
        $detailGroup->addDetail($detail);

        $this->assertCount(1, $detailGroup->getDetails());

        $detailGroup->removeDetail($detail->getKey());
        $this->assertCount(0, $detailGroup->getDetails());
    }

    /**
     * @covers DetailGroup::hasDetail
     * @depends testAddDetail
     * @depends testRemoveDetail
     */
    public function testHasDetail()
    {
        $detail = new Detail('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $detailGroup->addDetail($detail);
        $this->assertTrue($detailGroup->hasDetail($detail->getKey()));

        $detailGroup->removeDetail($detail->getKey());
        $this->assertFalse($detailGroup->hasDetail($detail->getKey()));
    }

    /**
     * @covers DetailGroup::getDetail
     * @depends testAddDetail
     * @depends testRemoveDetail
     */
    public function testGetDetail()
    {
        $detail = new Detail('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $this->assertNull($detailGroup->getDetail($detail->getKey()));

        $detailGroup->addDetail($detail);
        $this->assertNotNull($detailGroup->getDetail($detail->getKey()));

        $detailGroup->removeDetail($detail->getKey());
        $this->assertNull($detailGroup->getDetail($detail->getKey()));
    }

    /**
     * @covers DetailGroup::countDetails
     * @depends testAddDetail
     * @depends testRemoveDetail
     */
    public function testCountDetails()
    {
        $detail = new Detail('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $this->assertCount($detailGroup->countDetails(), $detailGroup->getDetails());

        $detailGroup->addDetail($detail);
        $this->assertCount($detailGroup->countDetails(), $detailGroup->getDetails());

        $detailGroup->removeDetail($detail->getKey());
        $this->assertCount($detailGroup->countDetails(), $detailGroup->getDetails());
    }
}
