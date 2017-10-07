<?php
namespace Affilicious_Tests\Common\Admin\License;

use Affilicious\Common\Admin\License\License_Status;

class License_Status_Test extends \WP_UnitTestCase
{
	/**
	 * @since 0.9.9
	 * @return array
	 */
	public function provide_data_for_static_factory_type()
	{
		return [
			[License_Status::success(), License_Status::SUCCESS],
			[License_Status::error(), License_Status::ERROR],
			[License_Status::valid(), License_Status::VALID],
			[License_Status::invalid(), License_Status::INVALID],
			[License_Status::missing(), License_Status::MISSING],
		];
	}

	/**
	 * @since 0.9.9
	 * @return array
	 */
	public function provide_data_for_static_factory_message()
	{
		return [
			[License_Status::success('Test'), 'Test'],
			[License_Status::error('Test'), 'Test']
		];
	}

	/**
	 * @dataProvider provide_data_for_static_factory_type
	 * @since 0.9.9
	 * @param License_Status $license_status
	 * @param string $type
	 */
	public function test_static_factory_type(License_Status $license_status, $type)
	{
		$this->assertEquals($type, $license_status->get_type());
	}

	/**
	 * @dataProvider provide_data_for_static_factory_message
	 * @since 0.9.9
	 * @param License_Status $license_status
	 * @param string $message
	 */
	public function test_static_factory_message(License_Status $license_status, $message)
	{
		$this->assertTrue($license_status->has_message());
		$this->assertEquals($message, $license_status->get_message());
	}
}
