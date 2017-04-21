<?php
namespace Tests\Affilicious;

use Affilicious\Common\Model\Slug;

class Sample_Test extends \WP_UnitTestCase
{
	/**
	 * A single example test.
	 */
	function test_sample()
    {
        $name = new Slug('affilicious');
		$this->assertEquals('affilicious', $name->get_value());
	}
}
