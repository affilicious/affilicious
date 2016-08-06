<?php
use Affilicious\ProductsPlugin\Product\Field\DetailGroup;
use Affilicious\ProductsPlugin\Product\Field\Field;

class TestDetailGroup extends WP_UnitTestCase
{
    /**
     * @covers DetailGroup::addDetail
     */
    public function testAddField()
    {
        $field = new Field('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $this->assertCount(0, $detailGroup->getFields());

        $detailGroup->addField($field);
        $this->assertCount(1, $detailGroup->getFields());
    }

    /**
     * @covers DetailGroup::removeDetail
     * @depends testAddField
     */
    public function testRemoveField()
    {
        $field = new Field('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');
        $detailGroup->addField($field);

        $this->assertCount(1, $detailGroup->getFields());

        $detailGroup->removeField($field->getKey());
        $this->assertCount(0, $detailGroup->getFields());
    }

    /**
     * @covers DetailGroup::hasField
     * @depends testAddField
     * @depends testRemoveField
     */
    public function testHasField()
    {
        $field = new Field('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $detailGroup->addField($field);
        $this->assertTrue($detailGroup->hasField($field->getKey()));

        $detailGroup->removeField($field->getKey());
        $this->assertFalse($detailGroup->hasField($field->getKey()));
    }

    /**
     * @covers DetailGroup::getField
     * @depends testAddField
     * @depends testRemoveField
     */
    public function testGetField()
    {
        $field = new Field('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $this->assertNull($detailGroup->getField($field->getKey()));

        $detailGroup->addField($field);
        $this->assertNotNull($detailGroup->getField($field->getKey()));

        $detailGroup->removeField($field->getKey());
        $this->assertNull($detailGroup->getField($field->getKey()));
    }

    /**
     * @covers DetailGroup::countFields
     * @depends testAddField
     * @depends testRemoveField
     */
    public function testCountFields()
    {
        $field = new Field('key', 'text', 'label');
        $detailGroup = new DetailGroup(1, 'test-title', 'test-category');

        $this->assertCount($detailGroup->countFields(), $detailGroup->getFields());

        $detailGroup->addField($field);
        $this->assertCount($detailGroup->countFields(), $detailGroup->getFields());

        $detailGroup->removeField($field->getKey());
        $this->assertCount($detailGroup->countFields(), $detailGroup->getFields());
    }
}
