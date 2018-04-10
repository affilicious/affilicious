<?php
use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Tracking;

class Tracking_Test extends WP_UnitTestCase
{
	/**
	 * @since 0.9.2
	 */
	public function test_affiliate_link()
	{
		$tracking = new Tracking(new Affiliate_Link('https://example.com'), new Affiliate_Product_Id('test'));

		$this->assertNotNull($tracking->get_affiliate_link());
		$this->assertEquals('https://example.com', $tracking->get_affiliate_link()->get_value());
	}

	/**
	 * @since 0.9.2
	 */
	public function test_affiliate_product_id()
	{
		$tracking = new Tracking(new Affiliate_Link('https://example.com'), new Affiliate_Product_Id('test'));

		$this->assertTrue($tracking->has_affiliate_product_id());
		$this->assertNotNull($tracking->get_affiliate_product_id());
		$this->assertEquals('test', $tracking->get_affiliate_product_id()->get_value());
	}
}
