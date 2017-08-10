<?php
namespace Tests\Affilicious\Attribute\Model;

use Affilicious\Provider\Model\Credentials;

class Credentials_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since 0.9.2
	 * @return array
	 */
	public function provide_data_for_has()
	{
		return [
			[new Credentials(['test' => 'test']), 'test', true],
			[new Credentials(['not_existing' => 'not_existing']), 'test', false]
		];
	}

	/**
	 * @since 0.9.2
	 * @dataProvider provide_data_for_has
	 * @param Credentials $credentials
	 * @param string $key
	 * @param bool $result
	 */
	public function test_has(Credentials $credentials, $key, $result)
	{
		$this->assertEquals($result, $credentials->has($key));
	}

	/**
	 * @since 0.9.2
	 * @return array
	 */
	public function provide_data_for_get()
	{
		return [
			[new Credentials(['test' => 'test']), 'test', 'test'],
			[new Credentials(['not_existing' => null]), 'not_existing', null],
			[new Credentials(['test' => 'test']), 'not_existing', null]
		];
	}

	/**
	 * @since 0.9.2
	 * @dataProvider provide_data_for_get
	 * @param Credentials $credentials
	 * @param string $key
	 * @param mixed $value
	 */
	public function test_get(Credentials $credentials, $key, $value)
	{
		$this->assertEquals($value, $credentials->get($key));
	}
}
