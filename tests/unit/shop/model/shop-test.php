<?php
namespace Tests\Affilicious\Detail\Model;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;
use Affilicious\Shop\Model\Pricing;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Tracking;

class Shop_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since 0.9.2
	 * @return array
	 */
	public function provide_data_for_is_cheaper_than()
	{
		return [
			[100, Currency::us_dollar(), 0, Currency::us_dollar(), false],
			[0, Currency::us_dollar(), 100, Currency::us_dollar(), true],
			[-100, Currency::us_dollar(), 0, Currency::us_dollar(), true],
			[0, Currency::us_dollar(), -100, Currency::us_dollar(), false],
			[100, Currency::euro(), 0, Currency::us_dollar(), false],
			[0, Currency::euro(), 100, Currency::us_dollar(), false],
			[-100, Currency::euro(), 0, Currency::us_dollar(), false],
			[0, Currency::euro(), -100, Currency::us_dollar(), false],
		];
	}

	/**
	 * @since 0.9.2
	 * @dataProvider provide_data_for_is_cheaper_than
	 * @param int $lh_value
	 * @param Currency $lh_currency
	 * @param int $rh_value
	 * @param Currency $rh_currency
	 * @param bool $result
	 */
	public function test_is_cheaper_than($lh_value, Currency $lh_currency, $rh_value, Currency $rh_currency, $result)
	{
		$expensive_shop = new Shop(
			new Name('Expensive shop'),
			new Slug('expensive-shop'),
			new Tracking(new Affiliate_Link('https://test.com')),
			new Pricing(Availability::available(), new Money($lh_value, $lh_currency))
		);

		$cheap_shop = new Shop(
			new Name('Cheap shop'),
			new Slug('cheap-shop'),
			new Tracking(new Affiliate_Link('https://test.com')),
			new Pricing(Availability::available(), new Money($rh_value, $rh_currency))
		);

		$this->assertEquals($result, $expensive_shop->is_cheaper_than($cheap_shop));
	}
}
