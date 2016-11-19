
/**
* @param Product_Interface $product
* @return Product_Interface|mixed|void
*/
public function update(Product_Interface $product)
{
do_action('affilicious_product_updater_before_update', $product);
$product = apply_filters('affilicious_updater_product_update', $product);
do_action('affilicious_product_updater_after_update', $product);

return $product;
}