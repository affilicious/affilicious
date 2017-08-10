<?php
namespace Tests\Affilicious\Shop\Model;

use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;

class Money_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since 0.9.2
	 */
	public function test_free_of_charge()
	{
		$money = Money::free_of_charge(Currency::us_dollar());

		$this->assertEquals(0, $money->get_value());
	}

	/**
	 * @since 0.9.2
	 */
	public function provide_data_for_equal_to()
	{
		return [
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::us_dollar()), true],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), false],
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
		];
	}

	/**
	 * @dataProvider provide_data_for_equal_to
	 * @since 0.9.2
	 * @param Money $lhv
	 * @param Money $rhv
	 * @param bool $result
	 */
	public function test_equal_to(Money $lhv, Money $rhv, $result)
	{
		$this->assertEquals($result, $lhv->is_equal_to($rhv));
	}

	/**
	 * @since 0.9.2
	 */
	public function provide_data_for_smaller_than()
	{
		return [
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::us_dollar()), false],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), true],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::us_dollar()), true],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), false],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::us_dollar()), false],
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::euro()), false],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::euro()), false],
		];
	}

	/**
	 * @dataProvider provide_data_for_smaller_than
	 * @since 0.9.2
	 * @param Money $lhv
	 * @param Money $rhv
	 * @param bool $result
	 */
	public function test_smaller_than(Money $lhv, Money $rhv, $result)
	{
		$this->assertEquals($result, $lhv->is_smaller_than($rhv));
	}

	/**
	 * @since 0.9.2
	 */
	public function provide_data_for_smaller_than_or_equal_to()
	{
		return [
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::us_dollar()), true],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), true],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::us_dollar()), true],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), false],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::us_dollar()), false],
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::euro()), false],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::euro()), false],
		];
	}

	/**
	 * @requires test_smaller_than
	 * @dataProvider provide_data_for_smaller_than_or_equal_to
	 * @since 0.9.2
	 * @param Money $lhv
	 * @param Money $rhv
	 * @param bool $result
	 */
	public function test_smaller_than_or_equal_to(Money $lhv, Money $rhv, $result)
	{
		$this->assertEquals($result, $lhv->is_smaller_than_or_equal_to($rhv));
	}

	/**
	 * @since 0.9.2
	 */
	public function provide_data_for_greater_than()
	{
		return [
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::us_dollar()), false],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), false],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::us_dollar()), false],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), true],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::us_dollar()), true],
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::euro()), false],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::euro()), false],
		];
	}

	/**
	 * @dataProvider provide_data_for_greater_than
	 * @since 0.9.2
	 * @param Money $lhv
	 * @param Money $rhv
	 * @param bool $result
	 */
	public function test_greater_than(Money $lhv, Money $rhv, $result)
	{
		$this->assertEquals($result, $lhv->is_greater_than($rhv));
	}

	/**
	 * @since 0.9.2
	 */
	public function provide_data_for_greater_than_or_equal_to()
	{
		return [
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::us_dollar()), true],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), false],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::us_dollar()), false],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::us_dollar()), true],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::us_dollar()), true],
			[new Money(0, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(-10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(10, Currency::euro()), false],
			[new Money(10, Currency::us_dollar()), new Money(0, Currency::euro()), false],
			[new Money(0, Currency::us_dollar()), new Money(-10, Currency::euro()), false],
		];
	}

	/**
	 * @requires test_greater_than
	 * @dataProvider provide_data_for_greater_than_or_equal_to
	 * @since 0.9.2
	 * @param Money $lhv
	 * @param Money $rhv
	 * @param bool $result
	 */
	public function test_greater_than_or_equal_to(Money $lhv, Money $rhv, $result)
	{
		$this->assertEquals($result, $lhv->is_greater_than_or_equal_to($rhv));
	}
}
