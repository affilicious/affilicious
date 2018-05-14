<?php
namespace Affilicious\Product\Migration;

use Affilicious\Common\Model\Slug;
use Affilicious\Detail\Model\Value;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Product\Repository\Product_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 * @var string
 */
final class Details_Migration
{
    /**
     * @since 0.8
     * @var Product_Repository_Interface
     */
    protected $product_repository;

    /**
     * @since 0.8
     * @var Detail_Template_Repository_Interface
     */
    protected $detail_template_repository;

    /**
     * @since 0.8
     * @param Product_Repository_Interface $product_repository
     * @param Detail_Template_Repository_Interface $detail_template_repository
     */
    public function __construct(
        Product_Repository_Interface $product_repository,
        Detail_Template_Repository_Interface $detail_template_repository
    ) {
        $this->product_repository = $product_repository;
        $this->detail_template_repository = $detail_template_repository;
    }

    /**
     * Migrate the old details to the new product.
     *
     * @since 0.8
     */
    public function migrate()
    {
        global $wpdb;

        $products = $this->product_repository->find_all();
        foreach ($products as $product) {
            if(!($product instanceof Simple_Product) && !($product instanceof Complex_Product)) {
                continue;
            }

            $fields = carbon_get_post_meta($product->get_id()->get_value(), '_affilicious_product_detail_groups', 'complex');
            if(!empty($fields)) {
                foreach ($fields as $field) {
                    unset($field['_type']);

                    $keys = array_keys($field);
                    foreach ($keys as $key) {
                        $slug = str_replace('_', '-', $key);
                        $detail_template = $this->detail_template_repository->find_one_by_slug(new Slug($slug));
                        if($detail_template === null) {
                            continue;
                        }

                        $value = isset($field[$key]) ? $field[$key] : null;
                        if(empty($value)) {
                            continue;
                        }

                        $detail = $detail_template->build(new Value($value));
                        $detail->set_template_id($detail_template->get_id());

                        $product->add_detail($detail);
                    }
                }

                $this->product_repository->store($product);


                $id = $product->get_id()->get_value();
                $wpdb->query("
                    DELETE postmeta
                    FROM $wpdb->postmeta postmeta
                    WHERE postmeta.meta_key LIKE '_affilicious_product_detail_groups%'
                    AND postmeta.post_id = $id
                ");
            }
        }
    }
}
