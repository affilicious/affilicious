<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Slug;
use Affilicious\Detail\Model\Detail;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Detail_Aware_Interface
{
    /**
     * Check if the product has a specific detail by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     * @return bool
     */
    public function has_detail(Slug $slug);

    /**
     * Add a new product detail.
     *
     * @since 0.8
     * @param Detail $detail
     */
    public function add_detail(Detail $detail);

    /**
     * Remove the product detail by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     */
    public function remove_detail(Slug $slug);

    /**
     * Get the product detail by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     * @return null|Detail
     */
    public function get_detail(Slug $slug);

    /**
     * Check if the product has any details.
     *
     * @since 0.8
     * @return bool
     */
    public function has_details();

    /**
     * Get the product details.
     *
     * @since 0.8
     * @return Detail[]
     */
    public function get_details();

    /**
     * Set the product details.
     * If you do this, the old details going to be replaced.
     *
     * @since 0.8
     * @param Detail[] $details
     */
    public function set_details($details);
}
