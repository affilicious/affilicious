<?php
namespace Affilicious\Product\Factory\In_Memory;

use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Factory\Simple_Product_Factory_Interface;
use Affilicious\Product\Model\Simple_Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Simple_Product_Factory implements Simple_Product_Factory_Interface
{
	/**
	 * @var Slug_Generator_Interface
	 */
	protected $slug_generator;

	/**
	 * @since 0.9.7
	 * @param Slug_Generator_Interface $slug_generator
	 */
	public function __construct(Slug_Generator_Interface $slug_generator)
	{
		$this->slug_generator = $slug_generator;
	}

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create(Name $name, Slug $slug)
    {
        do_action('aff_simple_product_factory_before_create');
        do_action('aff_product_factory_before_create');

        $simple_product = new Simple_Product($name, $slug);
        $simple_product = apply_filters('aff_simple_product_factory_create', $simple_product);
        $simple_product = apply_filters('aff_product_factory_create', $simple_product);

        do_action('aff_simple_product_factory_after_create');
        do_action('aff_product_factory_after_create');

        return $simple_product;
    }

	/**
	 * @inheritdoc
	 * @since 0.9.7
	 */
	public function create_from_name(Name $name)
	{
		$simple_product = $this->create(
			$name,
			$this->slug_generator->generate_from_name($name)
		);

		return $simple_product;
	}
}
