<?php
namespace Tests\Affilicious;

use Affilicious\Common\Model\Slug;
use PHPUnit\Framework\TestCase;

class Sample_Test extends TestCase
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
