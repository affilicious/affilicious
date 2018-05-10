<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * The currencies are based on ISO 4217.
 *
 * @since 0.8
 * @see https://de.wikipedia.org/wiki/ISO_4217
 */
class Currency
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * European Euro
	 *
	 * @since 0.10
	 * @var string
	 */
    const EUR = 'EUR';

	/**
	 * U.S. Dollar
	 *
	 * @since 0.10
	 * @var string
	 */
    const USD = 'USD';

	/**
	 * Japanese Yen
	 *
	 * @since 0.10
	 * @var string
	 */
    const JPY = 'JPY';

	/**
	 * British Pound
	 *
	 * @since 0.10
	 * @var string
	 */
    const GBP = 'GBP';

	/**
	 * Mexican peso
	 *
	 * @since 0.10
	 * @var string
	 */
    const MXN = 'MXN';

	/**
	 * Swiss Franc
	 *
	 * @since 0.10
	 * @var string
	 */
    const CHF = 'CHF';

    /**
     * Canadian Dollar
     *
	 * @since 0.10
	 * @var string
	 */
    const CAD = 'CAD';

	/**
	 * Australian Dollar
	 *
	 * @since 0.10
	 * @var string
	 */
    const AUD = 'AUD';

    /**
     * New Zealand Dollar
     *
	 * @since 0.10
	 * @var string
	 */
    const NZD = 'NZD';

    /**
     * South African Rand
     *
     * @since 0.10
	 * @var string
	 */
    const ZAR = 'ZAR';

	/**
	 * European Euro
	 *
	 * @deprecated 1.2 Use 'Currency::EUR' instead.
	 * @since 0.8
	 * @var string
	 */
    const EURO = 'EUR';

	/**
	 * U.S. Dollar
	 *
	 * @deprecated 1.2 Use 'Currency::USD' instead.
	 * @since 0.8
	 * @var string
	 */
    const US_DOLLAR = 'USD';

	/**
	 * @since 0.9.2
	 * @var array
	 */
    public static $all = [
    	self::EUR,
	    self::USD,
	    self::JPY,
	    self::GBP,
	    self::CHF,
	    self::MXN,
	    self::CAD,
	    self::AUD,
	    self::NZD,
	    self::ZAR,
    ];

	/**
	 * European Euro
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function eur()
	{
		return new self(self::EUR);
	}

	/**
	 * U.S. Dollar
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function usd()
	{
		return new self(self::USD);
	}

	/**
	 * Japanese Yen
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function jpy()
	{
		return new self(self::JPY);
	}

	/**
	 * British Pound
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function gbp()
	{
		return new self(self::GBP);
	}

	/**
	 * Swiss Franc
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function chf()
	{
		return new self(self::CHF);
	}

	/**
	 * Mexican peso
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function mxn()
	{
		return new self(self::MXN);
	}

	/**
	 * Canadian Dollar
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function cad()
	{
		return new self(self::CAD);
	}

	/**
	 * Australian Dollar
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function aud()
	{
		return new self(self::AUD);
	}

	/**
	 * New Zealand Dollar
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function nzd()
	{
		return new self(self::NZD);
	}

	/**
	 * South African Rand
	 *
	 * @since 0.10
	 * @return Currency
	 */
	public static function zar()
	{
		return new self(self::ZAR);
	}

    /**
     * European Euro
     *
     * @deprecated 1.2 Use 'Currency::eur()' instead.
     * @since 0.8
     * @return Currency
     */
    public static function euro()
    {
        return new self(self::EUR);
    }

    /**
     * @deprecated 1.2 Use 'Currency::usd()' instead.
     * @since 0.8
     * @return Currency
     */
    public static function us_dollar()
    {
        return new self(self::USD);
    }

	/**
	 * Get an array of currencies.
	 *
	 * @since 0.9.2
	 * @return Currency[]
	 */
    public static function all()
    {
    	$currencies = [];

    	foreach (self::$all as $value) {
    		$currencies[$value] = new self($value);
	    }

	    /**
	     * Filter the available currencies.
	     *
	     * @since 0.10
	     * @param array $currencies All available currencies.
	     * @return array All available currencies after filtering.
	     */
	    $currencies = apply_filters('aff_shop_currencies', $currencies);

	    return $currencies;
    }

	/**
	 * Get all translated currency labels.
	 *
	 * @since 0.10
	 * @return array All translated currency labels
	 */
    public static function labels()
    {
	    $labels = [
		    self::EUR => __('Euro', 'affilicious'),
		    self::USD => __('U.S. Dollar', 'affilicious'),
		    self::JPY => __('Yen', 'affilicious'),
		    self::GBP => __('Pound', 'affilicious'),
		    self::CHF => __('Franc', 'affilicious'),
		    self::MXN => __('Peso', 'affilicious'),
		    self::CAD => __('Canadian Dollar', 'affilicious'),
		    self::AUD => __('Australian Dollar', 'affilicious'),
		    self::NZD => __('New Zealand Dollar', 'affilicious'),
		    self::ZAR => __('Rand', 'affilicious'),
	    ];

	    /**
	     * Filter the translated currency labels.
	     *
	     * @since 0.10
	     * @param array $labels The translated currency labels.
	     * @return array The translated currency labels after filtering.
	     */
	    $labels = apply_filters('aff_shop_currency_labels', $labels);

	    return $labels;
    }

	/**
	 * Get all currency symbols.
	 *
	 * @since 0.10
	 * @return array All currency symbols
	 */
    public static function symbols()
    {
	    $symbols = [
		    self::EUR => '€',
		    self::USD => '$',
		    self::JPY => '¥',
		    self::GBP => '£',
		    self::CHF => '₣',
		    self::MXN => '$',
		    self::CAD => '$‎',
		    self::AUD => '$',
		    self::NZD => '$',
		    self::ZAR => 'R',
	    ];

	    /**
	     * Filter the currency symbols.
	     *
	     * @since 0.10
	     * @param array $symbols The currency symbols.
	     * @return array The currency symbols after filtering.
	     */
	    $symbols = apply_filters('aff_shop_currency_symbols', $symbols);

	    return $symbols;
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
	 * Check if the currency is USD (U.S. Dollar).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_usd()
	{
		return $this->value === self::USD;
	}

	/**
	 * Check if the currency is JPY (Japanese Yen).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_jpy()
	{
		return $this->value === self::JPY;
	}

	/**
	 * Check if the currency is EUR (European Euro).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_eur()
	{
		return $this->value === self::EUR;
	}

	/**
	 * Check if the currency is GBP (British Pound).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_gbp()
	{
		return $this->value === self::GBP;
	}

	/**
	 * Check if the currency is CHF (Swiss Franc).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_chf()
	{
		return $this->value === self::CHF;
	}

	/**
	 * Check if the currency is MXN (Mexican peso).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_mxn()
	{
		return $this->value === self::MXN;
	}

	/**
	 * Check if the currency is CAD (Canadian Dollar).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_cad()
	{
		return $this->value === self::CAD;
	}

	/**
	 * Check if the currency is AUD (Australian Dollar).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_aud()
	{
		return $this->value === self::AUD;
	}

	/**
	 * Check if the currency is NZD (New Zealand Dollar).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_nzd()
	{
		return $this->value === self::NZD;
	}

	/**
	 * Check if the currency is ZAR (South African Rand).
	 *
	 * @since 0.10
	 * @return bool
	 */
	public function is_zar()
	{
		return $this->value === self::ZAR;
	}

	/**
	 * Check if the currency is USD (U.S. Dollar).
	 *
	 * @deprecated 1.2 Use 'Currency::is_usd' instead.
	 * @since 0.9.2
	 * @return bool
	 */
	public function is_us_dollar()
	{
		return $this->value === self::USD;
	}

	/**
	 * Check if the currency is Eur (European Euro).
	 *
	 * @deprecated 1.2 Use 'Currency::is_eur' instead.
	 * @since 0.9.2
	 * @return bool
	 */
	public function is_euro()
	{
		return $this->value === self::EUR;
	}

    /**
     * Get the translated label for the currency.
     *
     * @since 0.8
     * @return null|string The translated currency label.
     */
    public function get_label()
    {
	    $labels = self::labels();
		$label = isset($labels[$this->value]) ? $labels[$this->value] : null;

	    /**
	     * Filter the translated currency label.
	     *
	     * @since 0.8
	     * @param string|null $label The translated currency label.
	     * @param string $value The currency ISO code based on ISO 4217.
	     * @return string|null The translated currency label after filtering.
	     */
	    $label = apply_filters('aff_shop_currency_label', $label, $this->value);

		return $label;
    }

    /**
     * Get the symbol for the currency.
     *
     * @since 0.8
     * @return null|string The currency symbol.
     */
    public function get_symbol()
    {
		$symbols = self::symbols();
	    $symbol = isset($symbols[$this->value]) ? $symbols[$this->value] : null;

	    /**
	     * Filter the currency symbol.
	     *
	     * @since 0.8
	     * @param string|null $label The currency symbol.
	     * @param string $value The currency ISO code based on ISO 4217.
	     * @return string|null The currency symbol after filtering.
	     */
	    $symbol = apply_filters('aff_shop_currency_symbol', $symbol, $this->value);

	    return $symbol;
    }
}
