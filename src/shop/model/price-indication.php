<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10.1
 */
class Price_Indication
{
	use Simple_Value_Trait {
		Simple_Value_Trait::__construct as private set_value;
	}

	/**
	 * Get the default price indication.
	 *
	 * @since 0.10.1
	 * @return Price_Indication
	 */
	public static function standard()
	{
		return new self(_x('Incl. 19 % VAT and excl. shipping costs.', 'Default price indication', 'affilicious'));
	}

    /**
     * @inheritdoc
     * @since 0.10.1
     */
    public function __construct($value)
    {
        Assert_Helper::is_string_not_empty($value, __METHOD__, 'The price indication must be a non empty string. Got: %s', '0.10.1');

        $this->set_value($value);
    }
}
