<?php /** @var array $shop_templates */ ?>

<script id="aff-amazon-import-config-template" type="text/template">
    <fieldset class="aff-amazon-import-config-group aff-amazon-import-config-group-shops">
        <legend class="aff-amazon-import-config-group-title"><?php _e('Shops', 'affilicious'); ?></legend>

        <?php foreach ($shop_templates as $shop_template): ?>
            <label class="aff-amazon-import-config-group-label aff-amazon-import-config-group-label-<?php echo esc_attr($shop_template['slug']); ?>" for="<?php echo esc_attr($shop_template['slug']); ?>">
                <input id="<?php echo esc_attr($shop_template['slug']); ?>" class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-<?php echo esc_attr($shop_template['slug']); ?>" name="shop" type="radio" value="<?php echo esc_attr($shop_template['slug']); ?>" <?php if($shop_template['slug'] == 'amazon'): ?>checked="checked"<?php endif; ?>>
                <?php echo esc_html($shop_template['name']); ?>
            </label>
        <?php endforeach; ?>

        <label class="aff-amazon-import-config-group-label" for="new-shop">
            <input id="new-shop" class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-new-shop" name="shop" type="radio" value="new-shop">
            <?php _e('Create new shop', 'affilicious'); ?>
        </label>

        <label class="aff-amazon-import-config-group-label" for="new-shop-name">
            <input disabled="disabled" type="text" class="aff-amazon-import-config-group-option-new-shop-name regular-text" name="new-shop-name" placeholder="<?php _e('Enter shop name...', 'affilicious'); ?>">
        </label>
    </fieldset>

    <fieldset class="aff-amazon-import-config-group aff-amazon-import-config-group-status">
        <legend class="aff-amazon-import-config-group-title"><?php _e('Status', 'affilicious'); ?></legend>

        <label class="aff-amazon-import-config-group-label" for="draft">
            <input class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-draft" name="status" type="radio" value="draft" checked="checked">
            <?php _e('Save product as draft', 'affilicious'); ?>
        </label>

        <label class="aff-amazon-import-config-group-label" for="publish">
            <input class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-publish" name="status" type="radio" value="publish">
            <?php _e('Publish product directly', 'affilicious'); ?>
        </label>
    </fieldset>

    <fieldset class="aff-amazon-import-config-group aff-amazon-import-config-group-actions">
        <legend class="aff-amazon-import-config-group-title"><?php _e('Actions', 'affilicious'); ?></legend>

        <label class="aff-amazon-import-config-group-label" for="new-product">
            <input class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-new-product" name="action" type="radio" value="new-product" checked="checked">
            <?php _e('Create new product', 'affilicious'); ?>
        </label>

        <label class="aff-amazon-import-config-group-label" for="merge-product">
            <input class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-merge-product" name="action" type="radio" value="merge-product">
            <?php _e('Merge with existing product', 'affilicious'); ?>
        </label>

        <label class="aff-amazon-import-config-group-label" for="merge-product-id">
            <input disabled="disabled" class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-merge-product-id" name="merge-product-id" type="text" value="" placeholder="<?php _e('Enter product name...', 'affilicious'); ?>">
        </label>

        <label class="aff-amazon-import-config-group-label" for="replace-product">
            <input class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-replace-product" name="action" type="radio" value="replace-product">
            <?php _e('Replace with existing product', 'affilicious'); ?>
        </label>

        <label class="aff-amazon-import-config-group-label" for="replace-product-id">
            <input disabled="disabled" class="aff-amazon-import-config-group-option aff-amazon-import-config-group-option-replace-product-id" name="replace-product-id" type="text" value="" placeholder="<?php _e('Enter product name...', 'affilicious'); ?>">
        </label>
    </fieldset>
</script>
