<?php
namespace Affilicious\Provider\Model\Amazon;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Country
{
    const CODE_GERMANY = 'de';
    const CODE_AMERICA = 'com';
    const CODE_ENGLAND = 'co.uk';
    const CODE_CANADA = 'ca';
    const CODE_FRANCE = 'fr';
    const CODE_JAPAN = 'co.jp';
    const CODE_ITALY = 'it';
    const CODE_CHINA = 'cn';
    const CODE_SPAIN = 'es';
    const CODE_INDIA = 'in';
    const CODE_BRAZIL = 'com.br';
    const CODE_MEXICO = 'com.mx';
    const CODE_AUSTRALIA = 'com.au';

    public static $all = [
    	self::CODE_GERMANY,
	    self::CODE_AMERICA,
	    self::CODE_ENGLAND,
	    self::CODE_CANADA,
	    self::CODE_FRANCE,
	    self::CODE_JAPAN,
	    self::CODE_ITALY,
	    self::CODE_CHINA,
	    self::CODE_SPAIN,
	    self::CODE_INDIA,
	    self::CODE_BRAZIL,
	    self::CODE_MEXICO,
	    self::CODE_AUSTRALIA,
    ];

    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    /**
     * Create the country for Germany
     *
     * @since 0.8
     * @return Country
     */
    public static function germany()
    {
        return new self(self::CODE_GERMANY);
    }

    /**
     * Create the country for America
     *
     * @since 0.8
     * @return Country
     */
    public static function america()
    {
        return new self(self::CODE_AMERICA);
    }

    /**
     * Create the country for England
     *
     * @since 0.8
     * @return Country
     */
    public static function england()
    {
        return new self(self::CODE_ENGLAND);
    }

    /**
     * Create the country for Canada
     *
     * @since 0.8
     * @return Country
     */
    public static function canada()
    {
        return new self(self::CODE_CANADA);
    }

    /**
     * Create the country for France
     *
     * @since 0.8
     * @return Country
     */
    public static function france()
    {
        return new self(self::CODE_FRANCE);
    }

    /**
     * Create the country for Japan
     *
     * @since 0.8
     * @return Country
     */
    public static function japan()
    {
        return new self(self::CODE_JAPAN);
    }

    /**
     * Create the country for Italy
     *
     * @since 0.8
     * @return Country
     */
    public static function italy()
    {
        return new self(self::CODE_ITALY);
    }

    /**
     * Create the country for China
     *
     * @since 0.8
     * @return Country
     */
    public static function china()
    {
        return new self(self::CODE_CHINA);
    }

    /**
     * Create the country for Spain
     *
     * @since 0.8
     * @return Country
     */
    public static function spain()
    {
        return new self(self::CODE_SPAIN);
    }

    /**
     * Create the country for India
     *
     * @since 0.8
     * @return Country
     */
    public static function india()
    {
        return new self(self::CODE_INDIA);
    }

    /**
     * Create the country for Brazil
     *
     * @since 0.8
     * @return Country
     */
    public static function brazil()
    {
        return new self(self::CODE_BRAZIL);
    }

    /**
     * Create the country for Mexico
     *
     * @since 0.8
     * @return Country
     */
    public static function mexico()
    {
        return new self(self::CODE_MEXICO);
    }

    /**
     * Create the country for Australia
     *
     * @since 0.8
     * @return Country
     */
    public static function australia()
    {
        return new self(self::CODE_AUSTRALIA);
    }

    /**
     * @since 0.8
     * @param string $value
     */
    public function __construct($value)
    {
        Assert_Helper::is_string_not_empty($value, __METHOD__, 'The country code must be a non empty string. Got: %s', '0.9.2');

        $this->set_value($value);
    }

	/**
	 * Get the translated label for the country.
	 *
	 * @since 0.9.2
	 * @return null|string The translated label if any.
	 */
    public function get_label()
    {
    	switch ($this->value) {
		    case self::CODE_GERMANY:
		    	$label = __('Germany', 'affilicious');
		    	break;
		    case self::CODE_AMERICA:
			    $label = __('America', 'affilicious');
			    break;
		    case self::CODE_ENGLAND:
			    $label = __('England', 'affilicious');
			    break;
		    case self::CODE_CANADA:
			    $label = __('Canada', 'affilicious');
			    break;
		    case self::CODE_FRANCE:
			    $label = __('France', 'affilicious');
			    break;
		    case self::CODE_JAPAN:
			    $label = __('Japan', 'affilicious');
			    break;
		    case self::CODE_ITALY:
			    $label = __('Italy', 'affilicious');
			    break;
		    case self::CODE_CHINA:
			    $label = __('China', 'affilicious');
			    break;
		    case self::CODE_SPAIN:
			    $label = __('Spain', 'affilicious');
			    break;
		    case self::CODE_INDIA:
			    $label = __('India', 'affilicious');
			    break;
		    case self::CODE_BRAZIL:
			    $label = __('Brazil', 'affilicious');
			    break;
		    case self::CODE_MEXICO:
			    $label = __('Mexico', 'affilicious');
			    break;
		    case self::CODE_AUSTRALIA:
			    $label = __('Australia', 'affilicious');
			    break;
		    default:
		    	$label = null;
	    }

	    $label = apply_filters('aff_provider_amazon_country_label', $label, $this->value);

    	return $label;
    }
}
