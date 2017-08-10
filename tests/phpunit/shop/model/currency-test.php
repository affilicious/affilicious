<?php
namespace Tests\Affilicious\Shop\Model;

use Affilicious\Shop\Model\Currency;

class Currency_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since 0.9.2
	 * @return array
	 */
	public function provide_data_for_is_currency()
	{
		return [
			[Currency::us_dollar(), 'is_us_dollar', true],
			[Currency::us_dollar(), 'is_euro', false],
			[Currency::euro(), 'is_us_dollar', false],
			[Currency::euro(), 'is_euro', true],
		];
	}

	/**
	 * @dataProvider provide_data_for_is_currency
	 * @since 0.9.2
	 * @param Currency $currency
	 * @param string $method
	 * @param bool $result
	 */
	public function test_is_currency(Currency $currency, $method, $result)
	{
		$this->assertEquals($currency->{$method}(), $result);
	}

	/**
	 * @since 0.9.2
	 * @return array
	 */
	public function provide_data_for_get_symbol()
	{
		return [
			[Currency::us_dollar(), '$'],
			[Currency::euro(), '€'],
		];
	}

	/**
	 * @dataProvider provide_data_for_get_symbol
	 * @since 0.9.2
	 * @param Currency $currency
	 * @param string $symbol
	 */
	public function test_get_symbol(Currency $currency, $symbol)
	{
		$this->assertEquals($symbol, $currency->get_symbol());
	}
}
