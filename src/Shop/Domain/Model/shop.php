<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Aggregate;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Exception\Invalid_Price_Currency_Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop extends Abstract_Aggregate
{
    /**
     * @var Shop_Template_Id
     */
    protected $template_id;

    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Key
     */
    protected $key;

    /**
     * @var Image
     */
    protected $thumbnail;

    /**
     * @var Affiliate_Link
     */
    protected $affiliate_link;

    /**
     * @var Affiliate_Id
     */
    protected $affiliate_id;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var Price
     */
    protected $old_price;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Affiliate_Link $affiliate_link
     * @param Currency $currency
     */
    public function __construct(Title $title, Name $name, Key $key, Affiliate_Link $affiliate_link, Currency $currency)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->affiliate_link = $affiliate_link;
        $this->currency = $currency;
    }

    /**
     * Check if the shop has a template ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_template_id()
    {
        return $this->template_id !== null;
    }

    /**
     * Get the shop template ID
     *
     * @since 0.6
     * @return null|Shop_Template_Id
     *
     */
    public function get_template_id()
    {
        return $this->template_id;
    }

    /**
     * Set the shop template ID
     *
     * @since 0.6
     * @param null|Shop_Template_Id
     * $template_id
     * @throws Invalid_Type_Exception
     */
    public function set_template_id($template_id)
    {
        if($template_id !== null && !($template_id instanceof Shop_Template_Id)) {
            throw new Invalid_Type_Exception($template_id, 'Affilicious\Shop\Domain\Model\Shop_Template_Id');
        }

        $this->template_id = $template_id;
    }

    /**
     * Get the title for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Get the key for database usage
     *
     * @since 0.6
     * @return Key
     */
    public function get_key()
    {
        return $this->key;
    }

    /**
     * Check if the shop has a thumbnail
     *
     * @since 0.6
     * @return bool
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the optional thumbnail
     *
     * @since 0.6
     * @return null|Image
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the optional thumbnail
     *
     * @since 0.6
     * @param null|Image $thumbnail
     */
    public function set_thumbnail($thumbnail)
    {
        if($thumbnail !== null && !($thumbnail instanceof Image)) {
            throw new Invalid_Type_Exception($thumbnail, 'Affilicious\Common\Domain\Model\Image\Image');
        }

        $this->thumbnail = $thumbnail;
    }

    /**
     * Get the affiliate link
     *
     * @since 0.6
     * @return Affiliate_Link
     */
    public function get_affiliate_link()
    {
        return $this->affiliate_link;
    }

    /**
     * Check if the shop has an affiliate ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_affiliate_id()
    {
        return $this->affiliate_id !== null;
    }

    /**
     * Get the optional affiliate ID
     *
     * @since 0.6
     * @return Affiliate_Id
     */
    public function get_affiliate_id()
    {
        return $this->affiliate_id;
    }

    /**
     * Set the optional affiliate ID
     *
     * @since 0.6
     * @param null|Affiliate_Id $affiliate_id
     */
    public function set_affiliate_id($affiliate_id)
    {
        if($affiliate_id !== null && !($affiliate_id instanceof Affiliate_Id)) {
            throw new Invalid_Type_Exception($affiliate_id, 'Affilicious\Shop\Domain\Model\Affiliate_Id');
        }

        $this->affiliate_id = $affiliate_id;
    }

    /**
     * Check if the shop has a price
     *
     * @since 0.6
     * @return bool
     */
    public function has_price()
    {
        return $this->price !== null;
    }

    /**
     * Get the price
     *
     * @since 0.6
     * @return null|Price
     */
    public function get_price()
    {
        return $this->price;
    }

    /**
     * Set the price or set it to null to keep it empty.
     *
     * @since 0.6
     * @param null|Price $price
     * @throws Invalid_Price_Currency_Exception
     */
    public function set_price($price)
    {
        $this->check_price_currency($price);
        $this->price = $price;
    }

    /**
     * Check if the shop has an old price
     *
     * @since 0.6
     * @return bool
     */
    public function has_old_price()
    {
        return $this->old_price !== null;
    }

    /**
     * Get the old price
     *
     * @since 0.6
     * @return null|Price
     */
    public function get_old_price()
    {
        return $this->old_price;
    }

    /**
     * Set the old price or set it to null to keep it empty.
     *
     * @since 0.6
     * @param null|Price $old_price
     * @throws Invalid_Price_Currency_Exception
     */
    public function set_old_price($old_price)
    {
        $this->check_price_currency($old_price);
        $this->old_price = $old_price;
    }

    /**
     * Get the currency
     *
     * @since 0.6
     * @return Currency
     */
    public function get_currency()
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            ($this->has_template_id() && $this->get_template_id()->is_equal_to($object->get_template_id()) || !$object->has_template_id()) &&
            $this->get_title()->is_equal_to($object->get_title()) &&
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_key()->is_equal_to($object->get_key()) &&
            ($this->has_thumbnail() && $this->get_thumbnail()->is_equal_to($object->get_thumbnail()) || !$object->has_thumbnail()) &&
            ($this->has_affiliate_id() && $this->get_affiliate_id()->is_equal_to($object->get_affiliate_id()) || !$object->has_affiliate_id()) &&
            $this->get_affiliate_link()->is_equal_to($object->get_affiliate_link()) &&
            ($this->has_price() && $this->get_price()->is_equal_to($object->get_price()) || !$object->has_price()) &&
            ($this->has_old_price() && $this->get_old_price()->is_equal_to($object->get_old_price()) || !$object->has_old_price()) &&
            $this->get_currency()->is_equal_to($object->get_currency());
    }

    /**
     * Check if the currency of the price matches the shop currency
     *
     * @since 0.6
     * @param Price $price
     * @throws Invalid_Price_Currency_Exception
     */
    protected function check_price_currency($price)
    {
        if(!empty($price) && !$this->currency->is_equal_to($price->get_currency())) {
            throw new Invalid_Price_Currency_Exception($price, $this->currency);
        }
    }
}
