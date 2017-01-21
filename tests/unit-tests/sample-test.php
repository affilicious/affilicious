<?php
namespace Affilicious\Tests\Unit_Tests;

use Affilicious\Common\Model\Slug;

class Sample_Test extends Unit_Test_Case
{
	/**
	 * A single example test.
	 */
	function test_sample() {
        $name = new Slug('Affilicious');
		$this->assertEquals('Affilicious', $name->get_value());
	}
}
