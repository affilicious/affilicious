<?php
namespace Affilicious\Attribute\Domain\Model\Attribute;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Value extends Abstract_Value_Object
{
	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws Invalid_Type_Exception
	 */
	public function __construct($value)
	{
		parent::__construct($value);
	}
}
