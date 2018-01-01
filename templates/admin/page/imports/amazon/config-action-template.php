<script id="aff-amazon-import-config-action-template" type="text/template">
    <div class="aff-import-config-group aff-panel">
        <div class="aff-panel-header">
            <h2 class="aff-panel-title"><?php _e('Actions', 'affilicious'); ?></h2>
        </div>

        <div class="aff-panel-body">
            <label class="aff-import-config-group-label">
                <input id="aff-amazon-import-config-group-action-option-new-product" class="aff-import-config-group-option" name="action" type="radio" value="new-product" checked>
		        <?php _e('Create new product', 'affilicious'); ?>
            </label>

            <label class="aff-import-config-group-label">
                <input id="aff-amazon-import-config-group-action-option-merge-product" class="aff-import-config-group-option" name="action" type="radio" value="merge-product">
		        <?php _e('Merge with existing product', 'affilicious'); ?>
            </label>

            <label class="aff-import-config-group-label">
                <input id="aff-amazon-import-config-group-action-option-merge-product-id" class="aff-import-config-group-option" name="merge-product-id" placeholder="<?php _e('Enter product name...', 'affilicious'); ?>" disabled>
            </label>
        </div>
    </div>
</script>
