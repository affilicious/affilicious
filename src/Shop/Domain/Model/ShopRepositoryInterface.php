<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\RepositoryInterface;
use Affilicious\Shop\Domain\Exception\MissingShopException;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

interface ShopRepositoryInterface extends RepositoryInterface
{
    /**
     * Store the given shop
     *
     * @since 0.6
     * @param Shop $shop
     * @return Shop
     */
    public function store(Shop $shop);

    /**
     * Delete the shop by the given ID
     *
     * @since 0.6
     * @param ShopId $shopId
     * @return Shop
     * @throws MissingShopException
     */
    public function delete(ShopId $shopId);

	/**
	 * Find a shop by the given ID.
	 * The shop ID is just a Wordpress post ID, because a shop is just a custom post type
	 *
	 * @since 0.6
	 * @param ShopId $id
	 * @return null|Shop
	 */
	public function findById(ShopId $id);

	/**
	 * Find all shops
	 *
	 * @since 0.3
	 * @return Shop[]
	 */
	public function findAll();
}
