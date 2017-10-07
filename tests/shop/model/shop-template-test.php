<?php
namespace Affilicious_Tests\Detail\Model;


use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Pricing;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Model\Tracking;

class Shop_Template_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since 0.9.2
	 */
	public function test_build()
	{
		$shop_template = new Shop_Template(new Name('test'), new Slug('test'));
		$shop = $shop_template->build(new Tracking(new Affiliate_Link('https://test.com')), Pricing::out_of_stock());

		$this->assertEquals($shop_template->get_name()->get_value(), $shop->get_name()->get_value());
		$this->assertEquals($shop_template->get_slug()->get_value(), $shop->get_slug()->get_value());
		$this->assertEquals('https://test.com', $shop->get_tracking()->get_affiliate_link()->get_value());
	}
}
