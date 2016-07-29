<?php
use Affilicious\ProductsPlugin\Product\Field\FieldGroup;
use Affilicious\ProductsPlugin\Product\Field\Field;

class TestFieldGroup extends WP_UnitTestCase
{
    /**
     * @covers FieldGroup::addField
     */
    public function testAddField()
    {
        $field = new Field('key', 'text', 'label');
        $fieldGroup = new FieldGroup(1, 'test-title', 'test-category');

        $this->assertCount(0, $fieldGroup->getFields());

        $fieldGroup->addField($field);
        $this->assertCount(1, $fieldGroup->getFields());
    }

    /**
     * @covers FieldGroup::removeField
     * @depends testAddField
     */
    public function testRemoveField()
    {
        $field = new Field('key', 'text', 'label');
        $fieldGroup = new FieldGroup(1, 'test-title', 'test-category');
        $fieldGroup->addField($field);

        $this->assertCount(1, $fieldGroup->getFields());

        $fieldGroup->removeField($field->getKey());
        $this->assertCount(0, $fieldGroup->getFields());
    }

    /**
     * @covers FieldGroup::hasField
     * @depends testAddField
     * @depends testRemoveField
     */
    public function testHasField()
    {
        $field = new Field('key', 'text', 'label');
        $fieldGroup = new FieldGroup(1, 'test-title', 'test-category');

        $fieldGroup->addField($field);
        $this->assertTrue($fieldGroup->hasField($field->getKey()));

        $fieldGroup->removeField($field->getKey());
        $this->assertFalse($fieldGroup->hasField($field->getKey()));
    }

    /**
     * @covers FieldGroup::getField
     * @depends testAddField
     * @depends testRemoveField
     */
    public function testGetField()
    {
        $field = new Field('key', 'text', 'label');
        $fieldGroup = new FieldGroup(1, 'test-title', 'test-category');

        $this->assertNull($fieldGroup->getField($field->getKey()));

        $fieldGroup->addField($field);
        $this->assertNotNull($fieldGroup->getField($field->getKey()));

        $fieldGroup->removeField($field->getKey());
        $this->assertNull($fieldGroup->getField($field->getKey()));
    }

    /**
     * @covers FieldGroup::countFields
     * @depends testAddField
     * @depends testRemoveField
     */
    public function testCountFields()
    {
        $field = new Field('key', 'text', 'label');
        $fieldGroup = new FieldGroup(1, 'test-title', 'test-category');

        $this->assertCount($fieldGroup->countFields(), $fieldGroup->getFields());

        $fieldGroup->addField($field);
        $this->assertCount($fieldGroup->countFields(), $fieldGroup->getFields());

        $fieldGroup->removeField($field->getKey());
        $this->assertCount($fieldGroup->countFields(), $fieldGroup->getFields());
    }
}
