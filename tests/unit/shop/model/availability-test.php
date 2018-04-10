<?php
use Affilicious\Shop\Model\Availability;

class Availability_Test extends WP_UnitTestCase
{
	/**
	 * @since 0.9.2
	 * @return array
	 */
	public function provide_data_for_is_availability()
	{
		return [
			[Availability::available(), 'is_available', true],
			[Availability::out_of_stock(), 'is_available', false],
			[Availability::available(), 'is_out_of_stock', false],
			[Availability::out_of_stock(), 'is_out_of_stock', true],
		];
	}

	/**
	 * @dataProvider provide_data_for_is_availability
	 * @since 0.9.2
	 * @param Availability $availability
	 * @param string $method
	 * @param bool $result
	 */
	public function test_is_availability(Availability $availability, $method, $result)
	{
		$this->assertEquals($availability->{$method}(), $result);
	}
}
