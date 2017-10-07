<?php
namespace Affilicious_Tests\Detail\Model;

use Affilicious\Detail\Model\Type;

class Type_Test extends \WP_UnitTestCase
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
			[Type::text(), 'is_file', false],
			[Type::text(), 'is_boolean', false],
			[Type::number(), 'is_text', false],
			[Type::number(), 'is_number', true],
			[Type::number(), 'is_file', false],
			[Type::number(), 'is_boolean', false],
			[Type::file(), 'is_text', false],
			[Type::file(), 'is_number', false],
			[Type::file(), 'is_file', true],
			[Type::file(), 'is_boolean', false],
			[Type::boolean(), 'is_text', false],
			[Type::boolean(), 'is_number', false],
			[Type::boolean(), 'is_file', false],
			[Type::boolean(), 'is_boolean', true],
		];
	}

	/**
	 * @dataProvider provide_data_for_is_type
	 * @since 0.9.2
	 * @param Type $type
	 * @param string $method
	 * @param bool $result
	 */
	public function test_is_type(Type $type, $method, $result)
	{
		$this->assertEquals($type->{$method}(), $result);
	}
}
