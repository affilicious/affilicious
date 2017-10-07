<?php
namespace Affilicious_Tests\Common\Admin\License;

use Affilicious\Common\Admin\License\License_Manager;
use Affilicious\Common\Admin\License\License_Status;

class License_Manager_Test extends \WP_UnitTestCase
{
	/**
	 * @since 0.9.9
	 * @return array
	 */
	public function provide_data_for_test_item_license_key_flow()
	{
		return [
			[Test_License_Handler::TEST_LICENSE_VALID, Test_License_Handler::TEST_LICENSE_VALID, true],
			[Test_License_Handler::TEST_LICENSE_INVALID, Test_License_Handler::TEST_LICENSE_VALID, false],
			[' ' . Test_License_Handler::TEST_LICENSE_VALID . ' ', Test_License_Handler::TEST_LICENSE_VALID, true],
		];
	}

	/**
	 * @since 0.9.9
	 * @return array
	 */
	public function provide_data_for_item_license_valid()
	{
		return [
			[true],
			[false]
		];
	}

	/**
	 * @since 0.9.9
	 * @return array
	 */
	public function provide_data_for_activate_and_deactivation_flow()
	{
		return [
			[Test_License_Handler::TEST_LICENSE_VALID, License_Status::SUCCESS, License_Status::VALID, License_Status::SUCCESS],
			[Test_License_Handler::TEST_LICENSE_INVALID, License_Status::ERROR, License_Status::ERROR, License_Status::ERROR],
			[Test_License_Handler::TEST_LICENSE_INVALID_ON_CHECK, License_Status::SUCCESS, License_Status::INVALID, License_Status::SUCCESS],
			[' ' . Test_License_Handler::TEST_LICENSE_VALID . ' ', License_Status::SUCCESS, License_Status::VALID, License_Status::SUCCESS],
		];
	}

	/**
	 * @dataProvider provide_data_for_test_item_license_key_flow
	 * @since 0.9.9
	 * @param string $new_license_key
	 * @param string $license_key_check
	 * @param bool $same
	 */
	public function test_item_license_key_flow($new_license_key, $license_key_check, $same)
	{
		$license_manager = $this->get_license_manager();

		// There shouldn't be a license.
		$license_key = $license_manager->find_item_license_key(Test_License_Handler::ITEM_KEY);
		$this->assertNull($license_key);

		// Store the item's license key.
		$license_manager->store_item_license_key(Test_License_Handler::ITEM_KEY, $new_license_key);
		$license_key = $license_manager->find_item_license_key(Test_License_Handler::ITEM_KEY);
		$this->assertEquals($same, $license_key_check === $license_key);

		// Delete the item's license key.
		$license_manager->delete_item_license_key(Test_License_Handler::ITEM_KEY);
		$license_key = $license_manager->find_item_license_key(Test_License_Handler::ITEM_KEY);
		$this->assertNull($license_key);
	}

	/**
	 * @dataProvider provide_data_for_item_license_valid
	 * @param bool $valid
	 */
	public function test_item_license_valid_flow($valid)
	{
		$license_manager = $this->get_license_manager();

		$item_key_valid = $license_manager->is_item_license_valid(Test_License_Handler::ITEM_KEY);
		$this->assertFalse($item_key_valid);

		$license_manager->store_item_license_valid(Test_License_Handler::ITEM_KEY, $valid);
		$item_key_valid = $license_manager->is_item_license_valid(Test_License_Handler::ITEM_KEY);
		$this->assertEquals($valid, $item_key_valid);

		$license_manager->delete_item_license_valid(Test_License_Handler::ITEM_KEY);
		$item_key_valid = $license_manager->is_item_license_valid(Test_License_Handler::ITEM_KEY);
		$this->assertFalse($item_key_valid);
	}

	/**
	 * @depends test_item_license_key_flow
	 * @depend
	 * @dataProvider provide_data_for_activate_and_deactivation_flow
	 * @since 0.9.9
	 * @param string $license_key
	 * @param string $activation_status
	 * @param string $check_status
	 * @param string $deactivation_status
	 */
	public function test_activate_and_deactivation_flow($license_key, $activation_status, $check_status, $deactivation_status)
	{
		$license_manager = $this->get_license_manager();

		// Activate the license key.
		$license_status = $license_manager->activate_item(Test_License_Handler::ITEM_KEY, $license_key);
		$this->assertEquals($activation_status, $license_status->get_type(), $license_status->get_message());

		$item_license_valid = $license_manager->is_item_license_valid(Test_License_Handler::ITEM_KEY);
		if($activation_status == License_Status::ERROR || $activation_status == License_Status::INVALID) {
			$this->assertFalse($item_license_valid);
		} else {
			$this->assertTrue($item_license_valid);
		}

		$item_license_key = $license_manager->find_item_license_key(Test_License_Handler::ITEM_KEY);
		if($activation_status == License_Status::ERROR || $activation_status == License_Status::INVALID) {
			$this->assertNull($item_license_key);
		} else {
			$this->assertEquals(trim($license_key), $item_license_key);
		}

		// Check the license key.
		$license_status = $license_manager->check_item(Test_License_Handler::ITEM_KEY);
		$this->assertEquals($check_status, $license_status->get_type(), $license_status->get_message());

		$item_license_valid = $license_manager->is_item_license_valid(Test_License_Handler::ITEM_KEY);
		if($check_status == License_Status::ERROR || $check_status == License_Status::INVALID) {
			$this->assertFalse($item_license_valid);
		} else {
			$this->assertTrue($item_license_valid);
		}

		$item_license_key = $license_manager->find_item_license_key(Test_License_Handler::ITEM_KEY);
		if($activation_status == License_Status::ERROR || $activation_status == License_Status::INVALID) {
			$this->assertNull($item_license_key);
		} else {
			$this->assertEquals(trim($license_key), $item_license_key);
		}

		// Deactivate the license key.
		$license_status = $license_manager->deactivate_item(Test_License_Handler::ITEM_KEY);
		$this->assertEquals($deactivation_status, $license_status->get_type(), $license_status->get_message());

		$item_license_valid = $license_manager->is_item_license_valid(Test_License_Handler::ITEM_KEY);
		$this->assertFalse($item_license_valid);

		$item_license_key = $license_manager->find_item_license_key(Test_License_Handler::ITEM_KEY);
		$this->assertNull($item_license_key);
	}

	/**
	 * @since 0.9.9
	 * @return License_Manager
	 */
	private function get_license_manager()
	{
		$license_manager = \Affilicious::get('affilicious.common.admin.license.manager');
		$license_handler = new Test_License_Handler();
		$license_manager->add_license_handler($license_handler);

		return $license_manager;
	}
}
