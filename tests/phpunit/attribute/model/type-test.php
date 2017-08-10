<?php
namespace Tests\Affilicious\Attribute\Model;

use Affilicious\Attribute\Model\Type;

class Type_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since 0.9.2
	 * @return array
	 */
	public function provide_data_for_is_type()
	{
		return [
			[Type::text(), 'is_text', true],
			[Type::text(), 'is_number', false],
			[Type::number(), 'is_text', false],
			[Type::number(), 'is_number', true],
		];
	}

	/**
	 * @dataProvider provide_data_for_is_type
	 * @since 0.9.2
	 * @param Type $type
	 * @param string $method
	 * @param bool $result
	 */
	function test_is_type($type, $method, $result)
	{
		$this->assertEquals($type->{$method}(), $result);
	}
}
