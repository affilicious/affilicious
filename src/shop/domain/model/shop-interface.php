<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Aggregate_Interface;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Exception\Invalid_Price_Currency_Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Interface extends Aggregate_Interface
{
    /**
     * @since 0.7
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Affiliate_Link $affiliate_link
     * @param Currency $currency
     */
    public function __construct(Title $title, Name $name, Key $key, Affiliate_Link $affiliate_link, Currency $currency);

    /**
     * Check if the shop has a template ID
     *
     * @since 0.7
     * @return bool
     */
    public function has_template_id();

    /**
     * Get the shop template ID
     *
     * @since 0.7
     * @return null|Shop_Template_Id
     *
     */
    public function get_template_id();

    /**
     * Set the shop template ID
     *
     * @since 0.7
     * @param null|Shop_Template_Id
     * $template_id
     * @throws Invalid_Type_Exception
     */
    public function set_template_id($template_id);

    /**
     * Get the title for display usage
     *
     * @since 0.7
     * @return Title
     */
    public function get_title();

    /**
     * Get the name for url usage
     *
     * @since 0.7
     * @return Name
     */
    public function get_name();

    /**
     * Get the key for database usage
     *
     * @since 0.7
     * @return Key
     */
    public function get_key();

    /**
     * Check if the shop has a thumbnail
     *
     * @since 0.7
     * @return bool
     */
    public function has_thumbnail();

    /**
     * Get the optional thumbnail
     *
     * @since 0.7
     * @return null|Image
     */
    public function get_thumbnail();

    /**
     * Set the optional thumbnail
     *
     * @since 0.7
     * @param null|Image $thumbnail
     */
    public function set_thumbnail($thumbnail);

    /**
     * Get the affiliate link
     *
     * @since 0.7
     * @return Affiliate_Link
     */
    public function get_affiliate_link();

    /**
     * Check if the shop has an affiliate ID
     *
     * @since 0.7
     * @return bool
     */
    public function has_affiliate_id();

    /**
     * Get the optional affiliate ID
     *
     * @since 0.7
     * @return Affiliate_Id
     */
    public function get_affiliate_id();

    /**
     * Set the optional affiliate ID
     *
     * @since 0.7
     * @param null|Affiliate_Id $affiliate_id
     */
    public function set_affiliate_id($affiliate_id);

    /**
     * Check if the shop has a price
     *
     * @since 0.7
     * @return bool
     */
    public function has_price();

    /**
     * Get the price
     *
     * @since 0.7
     * @return null|Price
     */
    public function get_price();

    /**
     * Set the price or set it to null to keep it empty.
     *
     * @since 0.7
     * @param null|Price $price
     * @throws Invalid_Price_Currency_Exception
     */
    public function set_price($price);

    /**
     * Check if the shop has an old price
     *
     * @since 0.7
     * @return bool
     */
    public function has_old_price();

    /**
     * Get the old price
     *
     * @since 0.7
     * @return null|Price
     */
    public function get_old_price();

    /**
     * Set the old price or set it to null to keep it empty.
     *
     * @since 0.7
     * @param null|Price $old_price
     * @throws Invalid_Price_Currency_Exception
     */
    public function set_old_price($old_price);

    /**
     * Get the currency
     *
     * @since 0.7
     * @return Currency
     */
    public function get_currency();
}
