<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\Factory_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new shop which can be stored into the database.
     *
     * @since 0.6
     * @param Shop_Template_Interface $shop_template
     * @param Affiliate_Link $affiliate_link
     * @param Currency $currency
     * @return Shop
     */
    public function create(Shop_Template_Interface $shop_template, Affiliate_Link $affiliate_link, Currency $currency);

    /**
     * Create a new shop from the template.
     *
     * @since 0.6
     * @param Shop_Template_Id $shop_template_id
     * @param mixed $data The structure of the data varies and depends on the implementation
     * @return null|Shop
     */
    public function create_from_template_id_and_data(Shop_Template_Id $shop_template_id, $data);
}
