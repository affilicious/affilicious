<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail_Template_Group_Id extends Abstract_Value_Object
{
	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws Invalid_Type_Exception
	 */
	public function __construct($value)
	{
        if (is_numeric($value)) {
            $value = intval($value);
        }

		if (!is_int($value)) {
			throw new Invalid_Type_Exception($value, 'int');
		}

		parent::__construct($value);
	}
}
