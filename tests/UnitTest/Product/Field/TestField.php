<?php
use Affilicious\ProductsPlugin\Product\Field\Field;

class TestField extends WP_UnitTestCase
{
    /**
     * @return array
     */
    public function dataProviderValidType()
    {
        return array_map(function($type) {
            return array($type);
        }, Field::$types);
    }

    /**
     * @return array
     */
    public function dataProviderInvalidType()
    {
        return array(
            array(1),
            array(1.0),
            array('invalid'),
        );
    }

    /**
     * @param string $type
     * @covers Field::assertValidType
     * @dataProvider dataProviderValidType
     */
    public function testValidType($type)
    {
        new Field('key', $type, 'label');
    }

    /**
     * @param string $type
     * @covers Field::assertValidType
     * @dataProvider dataProviderInvalidType
     */
    public function testInvalidType($type)
    {
        $this->setExpectedException('Affilicious\ProductsPlugin\Exception\InvalidOptionException');
        new Field('key', $type, 'label');
    }
}
