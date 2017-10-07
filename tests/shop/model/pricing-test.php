<?php
namespace Affilicious_Tests\Shop\Model;

use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;
use Affilicious\Shop\Model\Pricing;

class Pricing_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since 0.9.2
	 */
	public function test_available()
	{
		$pricing = Pricing::available(new Money(10, Currency::us_dollar()), new Money(100, Currency::us_dollar()));

		$this->assertNotNull($pricing->get_availability());
		$this->assertEquals($pricing->get_availability()->get_value(), Availability::AVAILABLE);
		$this->assertNotNull($pricing->get_price());
		$this->assertEquals($pricing->get_price()->get_value(), 10);
		$this->assertEquals($pricing->get_price()->get_currency()->get_value(), Currency::US_DOLLAR);
		$this->assertNotNull($pricing->get_old_price());
		$this->assertEquals($pricing->get_old_price()->get_value(), 100);
		$this->assertEquals($pricing->get_old_price()->get_currency()->get_value(), Currency::US_DOLLAR);
	}

	/**
	 * @since 0.9.2
	 */
	public function test_out_of_stock()
	{
		$pricing = Pricing::out_of_stock();

		$this->assertNotNull($pricing->get_availability());
		$this->assertEquals($pricing->get_availability()->get_value(), Availability::OUT_OF_STOCK);
		$this->assertNull($pricing->get_price());
		$this->assertNull($pricing->get_old_price());
	}
}
