<?php
use Affilicious\Common\Admin\License\License_Handler_Interface;
use Affilicious\Common\Admin\License\License_Status;

class Test_License_Handler implements License_Handler_Interface
{
	const ITEM_KEY = 'test';
	const TEST_LICENSE_VALID = 'valid';
	const TEST_LICENSE_INVALID = 'invalid';
	const TEST_LICENSE_INVALID_ON_CHECK = 'invalid-on-check';

	/**
	 * @inheritdoc
	 * @since 0.9.9
	 */
	public function get_item_name()
	{
		return 'Test';
	}

	/**
	 * @inheritdoc
	 * @since 0.9.9
	 */
	public function get_item_key()
	{
		return self::ITEM_KEY;
	}

	/**
	 * @inheritdoc
	 * @since 0.9.9
	 */
	public function activate_item($license)
	{
		if($license == self::TEST_LICENSE_VALID) {
			return License_Status::success();
		}

		if($license == self::TEST_LICENSE_INVALID_ON_CHECK) {
			return License_Status::success();
		}

		return License_Status::error();
	}

	/**
	 * @inheritdoc
	 * @since 0.9.9
	 */
	public function deactivate_item($license)
	{
		if($license == self::TEST_LICENSE_VALID) {
			return License_Status::success();
		}

		if($license == self::TEST_LICENSE_INVALID_ON_CHECK) {
			return License_Status::success();
		}

		return License_Status::error();
	}

	/**
	 * @inheritdoc
	 * @since 0.9.9
	 */
	public function check_item($license)
	{
		if($license == self::TEST_LICENSE_VALID) {
			return License_Status::valid();
		}

		if($license == self::TEST_LICENSE_INVALID_ON_CHECK) {
			return License_Status::invalid();
		}

		return License_Status::invalid();
	}
}
