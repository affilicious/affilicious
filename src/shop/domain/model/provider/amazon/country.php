<?php
namespace Affilicious\Shop\Domain\Model\Provider\Amazon;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Country extends Abstract_Value_Object
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

    /**
     * Create the country for Germany
     *
     * @since 0.7
     * @return Country
     */
    public static function germany()
    {
        return new self(self::CODE_GERMANY);
    }

    /**
     * Create the country for America
     *
     * @since 0.7
     * @return Country
     */
    public static function america()
    {
        return new self(self::CODE_AMERICA);
    }

    /**
     * Create the country for England
     *
     * @since 0.7
     * @return Country
     */
    public static function england()
    {
        return new self(self::CODE_ENGLAND);
    }

    /**
     * Create the country for Canada
     *
     * @since 0.7
     * @return Country
     */
    public static function canada()
    {
        return new self(self::CODE_CANADA);
    }

    /**
     * Create the country for France
     *
     * @since 0.7
     * @return Country
     */
    public static function france()
    {
        return new self(self::CODE_FRANCE);
    }

    /**
     * Create the country for Japan
     *
     * @since 0.7
     * @return Country
     */
    public static function japan()
    {
        return new self(self::CODE_JAPAN);
    }

    /**
     * Create the country for Italy
     *
     * @since 0.7
     * @return Country
     */
    public static function italy()
    {
        return new self(self::CODE_ITALY);
    }

    /**
     * Create the country for China
     *
     * @since 0.7
     * @return Country
     */
    public static function china()
    {
        return new self(self::CODE_CHINA);
    }

    /**
     * Create the country for Spain
     *
     * @since 0.7
     * @return Country
     */
    public static function spain()
    {
        return new self(self::CODE_SPAIN);
    }

    /**
     * Create the country for India
     *
     * @since 0.7
     * @return Country
     */
    public static function india()
    {
        return new self(self::CODE_INDIA);
    }

    /**
     * Create the country for Brazil
     *
     * @since 0.7
     * @return Country
     */
    public static function brazil()
    {
        return new self(self::CODE_BRAZIL);
    }

    /**
     * Create the country for Mexico
     *
     * @since 0.7
     * @return Country
     */
    public static function mexico()
    {
        return new self(self::CODE_MEXICO);
    }

    /**
     * Create the country for Australia
     *
     * @since 0.7
     * @return Country
     */
    public static function australia()
    {
        return new self(self::CODE_AUSTRALIA);
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        $codes = array(
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
            self::CODE_AUSTRALIA
        );

        if (!is_string($value)) {
            throw new Invalid_Type_Exception($value, 'string');
        }

        if(!in_array($value, $codes)) {
            throw new \InvalidArgumentException(sprintf(
                'The country code "%" is not valid. Please choose from "%s"',
                $value,
                implode(', ', $codes)
            ));
        }

        parent::__construct($value);
    }
}
