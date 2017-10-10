<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_title', $product); ?>

<h1 class="aff-product-title"><?php the_title(); ?></h1>

<?php do_action('affilicious_template_after_product_universal_box_title', $product); ?>
