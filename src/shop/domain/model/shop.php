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

class Shop extends Abstract_Aggregate implements Shop_Interface
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
     * @inheritdoc
     * @since 0.7
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
     * @inheritdoc
     * @since 0.7
     */
    public function has_template_id()
    {
        return $this->template_id !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     *
     */
    public function get_template_id()
    {
        return $this->template_id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_template_id($template_id)
    {
        if($template_id !== null && !($template_id instanceof Shop_Template_Id)) {
            throw new Invalid_Type_Exception($template_id, 'Affilicious\Shop\Domain\Model\Shop_Template_Id');
        }

        $this->template_id = $template_id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_key()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_thumbnail($thumbnail)
    {
        if($thumbnail !== null && !($thumbnail instanceof Image)) {
            throw new Invalid_Type_Exception($thumbnail, 'Affilicious\Common\Domain\Model\Image\Image');
        }

        $this->thumbnail = $thumbnail;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_affiliate_link()
    {
        return $this->affiliate_link;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_affiliate_id()
    {
        return $this->affiliate_id !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_affiliate_id()
    {
        return $this->affiliate_id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_affiliate_id($affiliate_id)
    {
        if($affiliate_id !== null && !($affiliate_id instanceof Affiliate_Id)) {
            throw new Invalid_Type_Exception($affiliate_id, 'Affilicious\Shop\Domain\Model\Affiliate_Id');
        }

        $this->affiliate_id = $affiliate_id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_price()
    {
        return $this->price !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_price()
    {
        return $this->price;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_price($price)
    {
        $this->check_price_currency($price);
        $this->price = $price;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_old_price()
    {
        return $this->old_price !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_old_price()
    {
        return $this->old_price;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_old_price($old_price)
    {
        $this->check_price_currency($old_price);
        $this->old_price = $old_price;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_currency()
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     * @since 0.7
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
     * @since 0.7
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
