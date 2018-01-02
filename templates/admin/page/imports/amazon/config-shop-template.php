<?php
/**
 * @var array $shop_templates
 */
?>

<script id="aff-amazon-import-config-shop-template" type="text/template">
    <div class="aff-import-config-group aff-panel">
        <div class="aff-panel-header">
            <h2 class="aff-panel-title"><?php _e('Shops', 'affilicious'); ?></h2>
        </div>

        <div class="aff-panel-body">
            <% _.each(addedShops, function(addedShop, i) { %>
                <label class="aff-import-config-group-label" for="<%= addedShop.slug %>">
                    <input id="<%= addedShop.slug %>" class="aff-import-config-group-option" name="shop" type="radio" value="<%= addedShop.slug %>"<% if(shop == addedShop.slug) { %> checked<% } %>>
                    <%= addedShop.name %>
                </label>
            <% }, shop); %>

            <?php foreach ($shop_templates as $index => $shop_template): ?>
                <label class="aff-import-config-group-label" for="<?php echo esc_attr($shop_template['slug']); ?>">
                    <input id="<?php echo esc_attr($shop_template['slug']); ?>" class="aff-import-config-group-option" name="shop" type="radio" value="<?php echo esc_attr($shop_template['slug']); ?>"<?php if($index == 0): ?> checked<?php endif; ?><% if(shop == '<?php echo esc_attr($shop_template['slug']); ?>') { %> checked<% } %>>
                    <?php echo esc_html($shop_template['name']); ?>
                </label>
            <?php endforeach; ?>

            <label class="aff-import-config-group-label">
                <input id="new-shop" class="aff-import-config-group-option aff-amazon-import-config-group-option-new-shop" name="shop" type="radio" value="new-shop"<?php if(empty($shop_templates)): ?> checked<?php endif; ?><% if(shop == 'new-shop') { %> checked<% } %>>
                <?php _e('Create new shop', 'affilicious'); ?>
            </label>

            <label class="aff-import-config-group-label">
                <input class="aff-import-config-group-option-new-shop-name regular-text" name="new-shop-name" value="<%= newShopName %>" placeholder="<?php _e('Enter shop name...', 'affilicious'); ?>"<% if(shop != 'new-shop') { %> disabled<% } %>>
            </label>
        </div>
    </div>
</script>
