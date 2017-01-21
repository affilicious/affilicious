<?php
namespace Affilicious\Attribute\Model;

use Affilicious\Common\Exception\Invalid_Type_Exception;
use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Id
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * @inheritdoc
	 * @since 0.8
	 * @throws Invalid_Type_Exception
	 */
	public function __construct($value)
	{
        if (is_numeric($value)) {
            $value = intval($value);
        }

        Assert::integer($value, 'Expected attribute template ID to be an integer. Got: %s');

		$this->set_value($value);
	}

    /**
     * Get the value of the attribute template ID.
     *
     * @since 0.8
     * @return string
     */
    public function get_value()
    {
        return apply_filters('affilicious_attribute_template_id_get_value', $this->value);
    }
}
