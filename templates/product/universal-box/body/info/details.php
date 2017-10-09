<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_details', $product); ?>

<?php $details = aff_get_product_details($product); ?>
<?php if(!empty($details)): ?>
    <table class="aff-product-details">
        <tbody>
            <?php foreach ($details as $detail): ?>
                <tr class="aff-product-details-item" data-detail-slug="<?php echo $detail['slug']; ?>" data-detail-type="<?php echo $detail['type']; ?>">
                    <td class="aff-product-details-item-name"><?php echo $detail['name']; ?></td>
                    <?php if($detail['type'] == 'file'): ?>
                        <td class="aff-product-details-item-value-file aff-product-details-item-value"><?php echo wp_get_attachment_link($detail['value'], 'medium', false, false, __('Download', 'affilicious-shop-theme')); ?></td>
                    <?php elseif($detail['type'] == 'boolean'): ?>
                        <td class="aff-product-details-item-value-boolean aff-product-details-item-value"><?php if($detail['value']): ?><i class="aff-product-details-item-value-boolean-check"></i><?php else: ?><i class="fa fa-close text-danger"></i><?php endif; ?></td>
                    <?php else: ?>
                        <td class="aff-product-details-item-value-text aff-product-details-item-value"><?php echo esc_html($detail['value']); ?> <?php echo esc_html($detail['unit']); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php do_action('affilicious_template_before_product_details', $product); ?>
