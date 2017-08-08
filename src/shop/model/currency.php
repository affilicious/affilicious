<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Currency
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    const EURO = 'EUR';
    const US_DOLLAR = 'USD';

    public static $all = [
    	self::EURO,
	    self::US_DOLLAR
    ];

    /**
     * Get an Euro currency.
     *
     * @since 0.8
     * @return Currency
     */
    public static function euro()
    {
        return new self(self::EURO);
    }

    /**
     * Get a US-Dollar currency.
     *
     * @since 0.8
     * @return Currency
     */
    public static function us_dollar()
    {
        return new self(self::US_DOLLAR);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function __construct($value)
    {
        Assert_Helper::is_string_not_empty($value, __METHOD__, 'The currency must be a non empty string. Got: %s', '0.9.2');

        $this->set_value($value);
    }

    /**
     * Get the translated label for the currency.
     *
     * @since 0.8
     * @return null|string The translated label if any.
     */
    public function get_label()
    {
		switch ($this->value) {
			case self::EURO:
				$label = __('Euro', 'affilicious');
				break;
			case self::US_DOLLAR:
				$label = __('US-Dollar', 'affilicious');
				break;
			default:
				$label = null;
		}

	    $label = apply_filters('aff_shop_currency_label', $label, $this->value);

		return $label;
    }

    /**
     * Get the symbol for the currency.
     *
     * @since 0.8
     * @return null|string The symbol if any.
     */
    public function get_symbol()
    {
	    switch ($this->value) {
		    case self::EURO:
			    $symbol = '€';
			    break;
		    case self::US_DOLLAR:
			    $symbol = '$';
			    break;
		    default:
			    $symbol = null;
	    }

	    $symbol = apply_filters('aff_shop_currency_symbol', $symbol, $this->value);

	    return $symbol;
    }
}
