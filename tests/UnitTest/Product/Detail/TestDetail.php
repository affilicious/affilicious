<?php
use Affilicious\ProductsPlugin\Product\Detail\Detail;

class TestDetail extends WP_UnitTestCase
{
    /**
     * @return array
     */
    public function dataProviderValidType()
    {
        return array_map(function($type) {
            return array($type);
        }, Detail::$types);
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
     * @covers Detail::assertValidType
     * @dataProvider dataProviderValidType
     */
    public function testValidType($type)
    {
        new Detail('key', $type, 'label');
    }

    /**
     * @param string $type
     * @covers Detail::assertValidType
     * @dataProvider dataProviderInvalidType
     */
    public function testInvalidType($type)
    {
        $this->setExpectedException('Affilicious\ProductsPlugin\Exception\InvalidOptionException');
        new Detail('key', $type, 'label');
    }

    /**
     * @covers Detail::hasValue
     */
    public function testHasValue()
    {
        $detail = new Detail('key', 'text', 'label');
        $this->assertFalse($detail->hasValue());

        $detail = new Detail('key', 'text', 'label', 'value');
        $this->assertTrue($detail->hasValue());
    }

    /**
     * @covers Detail::getDownloadLink()
     * @depends testValidType
     */
    public function testGetDownloadLink()
    {
        $postId = $this->factory->post->create();
        $attachmentId = $this->factory->attachment->create_object('data/cat.jpg', $postId, array(
            'post_mime_type' => 'image/jpeg',
            'post_excerpt'   => 'A sample caption',
        ));
        $detail = new Detail('key', 'file', 'attachment', $attachmentId);
        $this->assertTrue($detail->isFile());

        $link = $detail->getDownloadLink();
        $this->assertNotEquals(false, filter_var($link, FILTER_VALIDATE_URL));
    }
}
