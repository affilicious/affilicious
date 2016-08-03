<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

use Affilicious\ProductsPlugin\Product\Domain\Exception\InvalidOptionException;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class PriceComparison
{
    const DEFAULT_POSITION_NONE = 'none';
    const DEFAULT_POSITION_TOP = 'top';
    const DEFAULT_POSITION_BOTTOM = 'bottom';
    const DEFAULT_POSITION_BOTH = 'both';

    public static $defaultPositions = array(
        self::DEFAULT_POSITION_NONE,
        self::DEFAULT_POSITION_TOP,
        self::DEFAULT_POSITION_BOTTOM,
        self::DEFAULT_POSITION_BOTH
    );

    /**
     * The default position for the price comparison on the product page
     * You can always add additional positions into the content with the related shortcodes
     * @var string
     */
    private $defaultPosition;

    /**
     * European Article Number (EAN) is a unique ID used for identification of retail products
     * @var string
     */
    private $ean;

    /**
     * The specific shops with all information for the price comparison like Amazon, Affilinet or Ebay.
     * It's stored as an array where each entry is another key-value array for the specific shop
     * @var array
     */
    private $shops;

    /**
     * @param string $defaultPosition
     */
    public function __construct($defaultPosition = self::DEFAULT_POSITION_BOTH)
    {
        $this->defaultPosition = $defaultPosition;
        $this->shops = array();
    }

    /**
     * Get the default position on the product page
     * @return string
     */
    public function getDefaultPosition()
    {
        return $this->defaultPosition;
    }

    /**
     * Set the default position on the product page
     * @param string $defaultPosition
     */
    public function setDefaultPosition($defaultPosition)
    {
        if (!in_array($defaultPosition, self::$defaultPositions)) {
            throw new InvalidOptionException($defaultPosition, self::$defaultPositions);
        }

        $this->defaultPosition = $defaultPosition;
    }

    /**
     * Check if the price comparision has any European Article Number (EAN)
     * @return bool
     */
    public function hasEan()
    {
        return $this->ean !== null;
    }

    /**
     * Get the European Article Number (EAN)
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * Set the European Article Number (EAN)
     * @param string $ean
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
    }

    /**
     * Check if the price comparison has any shops
     */
    public function hasShops()
    {
        return !empty($this->shops);
    }

    /**
     * Get the shops
     * @return array
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * Set the shops
     * @param array $shops
     */
    public function setShops(array $shops)
    {
        $this->shops = $shops;
    }
}
