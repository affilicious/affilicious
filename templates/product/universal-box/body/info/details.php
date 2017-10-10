<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_details', $product); ?>

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
                        <td class="aff-product-details-item-value-boolean aff-product-details-item-value">
                            <?php if($detail['value'] == 'yes'): ?>
                                <svg class="aff-product-details-item-value-boolean-check" viewBox="0 0 1792 1792" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z" transform="matrix(1, 0, 0, 1, 0, 2.842170943040401e-14)"/>
                                </svg>
                            <?php else: ?>
                                <svg class="aff-product-details-item-value-boolean-cross" viewBox="0 0 1792 1792" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z" transform="matrix(1, 0, 0, 1, 0, 2.842170943040401e-14)"/>
                                </svg>
                            <?php endif; ?>
                        </td>
                    <?php else: ?>
                        <td class="aff-product-details-item-value-text aff-product-details-item-value"><?php echo esc_html($detail['value']); ?> <?php echo esc_html($detail['unit']); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php do_action('affilicious_template_after_product_universal_box_details', $product); ?>
