<?php
use Affilicious\ProductsPlugin\Product\Field\Field;

class SampleTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_sample() {
        $field = new Field('key', 'type', 'label');
		$this->assertEquals($field->getKey(), 'key');
	}
}
