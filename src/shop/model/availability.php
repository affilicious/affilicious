<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Availability
{
	use Simple_Value_Trait {
		Simple_Value_Trait::__construct as private set_value;
	}

	/**
	 * @since 0.8
	 * @var string
	 */
    const AVAILABLE = 'available';

	/**
	 * @since 0.8
	 * @var string
	 */
    const OUT_OF_STOCK = 'out-of-stock';

	/**
	 * @since 0.8
	 * @var array
	 */
    public static $all = [
        self::AVAILABLE,
	    self::OUT_OF_STOCK
    ];

    /**
     * Create the availability for available.
     *
     * @since 0.8
     * @return Availability
     */
    public static function available()
    {
        return new self(self::AVAILABLE);
    }

    /**
     * Create the availability for out of stock.
     *
     * @since 0.8
     * @return Availability
     */
    public static function out_of_stock()
    {
        return new self(self::OUT_OF_STOCK);
    }

	/**
	 * Create an array of all availabilities.
	 *
	 * @since 0.9.2
	 * @return array
	 */
    public static function all()
    {
    	$all = [];

    	foreach (self::$all as $value) {
    		$all[] = new self($value);
	    }

	    return $all;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function __construct($value)
    {
        Assert_Helper::is_string_not_empty($value, __METHOD__, 'The availability must be a non empty string. Got: %s', '0.9.2');

        $this->set_value($value);
    }

    /**
     * Check if the availability is available.
     *
     * @since 0.8
     * @return bool Whether the availability is available or not.
     */
    public function is_available()
    {
        return $this->value === self::AVAILABLE;
    }

    /**
     * Check if the availability is out of stock.
     *
     * @since 0.8
     * @return bool Whether the availability is out of stock or not.
     */
    public function is_out_of_stock()
    {
        return $this->value === self::OUT_OF_STOCK;
    }

	/**
	 * Get the translated label for the current value.
	 *
	 * @since 0.9.2
	 * @return string|null The translated label if any.
	 */
    public function get_label()
    {
    	switch($this->value) {
		    case self::AVAILABLE:
		    	$label = __('Available', 'affilicious');
		    	break;
		    case self::OUT_OF_STOCK:
			    $label = __('Out of stock', 'affilicious');
			    break;
		    default:
		    	$label = null;
	    }

	    $label = apply_filters('aff_shop_availability_label', $label, $this->value);

    	return $label;
    }
}
