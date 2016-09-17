<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\RepositoryInterface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

interface ShopRepositoryInterface extends RepositoryInterface
{
	/**
	 * Find a shop by the given ID.
	 * The shop ID is just a Wordpress post ID, because a shop is just a custom post type
	 *
	 * @since 0.5.2
	 * @param ShopId $id
	 * @return Shop|null
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
