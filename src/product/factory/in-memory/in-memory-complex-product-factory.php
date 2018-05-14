<?php
namespace Affilicious\Product\Factory\In_Memory;

use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Factory\Complex_Product_Factory_Interface;
use Affilicious\Product\Model\Complex_Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class In_Memory_Complex_Product_Factory implements Complex_Product_Factory_Interface
{
	/**
	 * @since 0.8
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
        do_action('aff_complex_product_factory_before_create');
        do_action('aff_product_factory_before_create');

        $complex_product = new Complex_Product($name, $slug);
        $complex_product = apply_filters('aff_complex_product_factory_create', $complex_product);
        $complex_product = apply_filters('aff_product_factory_create', $complex_product);

        do_action('aff_complex_product_factory_after_create', $complex_product);
        do_action('aff_product_factory_after_create', $complex_product);

        return $complex_product;
    }

	/**
	 * @inheritdoc
	 * @since 0.9.7
	 */
	public function create_from_name(Name $name)
	{
		$complex_product = $this->create(
			$name,
			$this->slug_generator->generate_from_name($name)
		);

		return $complex_product;
	}
}
