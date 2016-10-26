<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Domain\Model\Repository_Interface;
use Affilicious\Shop\Domain\Exception\Shop_Template_Database_Exception;
use Affilicious\Shop\Domain\Exception\Shop_Template_Not_Found_Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

interface Shop_Template_Repository_Interface extends Repository_Interface
{
    /**
     * Store the given shop template.
     * The ID and the name of the returned shop template might be different.
     *
     * @since 0.6
     * @param Shop_template $shop_template
     * @return Shop_Template
     */
    public function store(Shop_Template $shop_template);

    /**
     * Delete the shop template by the given ID.
     * The ID of returned shop template might be null.
     *
     * @since 0.6
     * @param Shop_Template_Id $shop_template_id
     * @return Shop_Template
     * @throws Shop_Template_Not_Found_Exception
     * @throws Invalid_Post_Type_Exception
     * @throws Shop_Template_Database_Exception
     */
    public function delete(Shop_Template_Id $shop_template_id);

	/**
	 * Find a shop template by the given ID.
	 * The shop template ID is just a Wordpress post ID, because a shop template is just a custom post type.
	 *
	 * @since 0.6
	 * @param Shop_Template_Id $shop_template_id
	 * @return null|Shop_Template
     * @throws Invalid_Post_Type_Exception
	 */
	public function find_by_id(Shop_Template_Id $shop_template_id);

	/**
	 * Find all shop templates.
	 *
	 * @since 0.6
	 * @return Shop_Template[]
	 */
	public function find_all();
}
