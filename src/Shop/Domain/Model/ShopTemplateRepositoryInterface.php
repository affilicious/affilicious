<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\RepositoryInterface;
use Affilicious\Shop\Domain\Exception\ShopTemplateDatabaseException;
use Affilicious\Shop\Domain\Exception\ShopTemplateNotFoundException;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

interface ShopTemplateRepositoryInterface extends RepositoryInterface
{
    /**
     * Store the given shop template.
     * The ID and the name of the returned shop template might be different.
     *
     * @since 0.6
     * @param ShopTemplate $shopTemplate
     * @return ShopTemplate
     */
    public function store(ShopTemplate $shopTemplate);

    /**
     * Delete the shop template by the given ID.
     * The ID of returned shop template might be null.
     *
     * @since 0.6
     * @param ShopTemplateId $shopTemplateId
     * @return ShopTemplate
     * @throws ShopTemplateNotFoundException
     * @throws InvalidPostTypeException
     * @throws ShopTemplateDatabaseException
     */
    public function delete(ShopTemplateId $shopTemplateId);

	/**
	 * Find a shop template by the given ID.
	 * The shop template ID is just a Wordpress post ID, because a shop template is just a custom post type.
	 *
	 * @since 0.6
	 * @param ShopTemplateId $shopTemplateId
	 * @return null|ShopTemplate
     * @throws InvalidPostTypeException
	 */
	public function findById(ShopTemplateId $shopTemplateId);

	/**
	 * Find all shop templates.
	 *
	 * @since 0.6
	 * @return ShopTemplate[]
	 */
	public function findAll();
}
