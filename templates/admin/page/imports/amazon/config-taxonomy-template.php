<?php
/**
 * @var array $taxonomies
 */
?>

<script id="aff-amazon-import-config-taxonomy-template" type="text/template">
    <div class="aff-import-config-group aff-panel">
        <div class="aff-import-config-group-header aff-panel-header">
            <h2 class="aff-panel-title"><?php _e('Taxonomy', 'affilicious'); ?></h2>
        </div>

        <div class="aff-import-config-group-body aff-panel-body">
            <label class="screen-reader-text" for="aff-amazon-status-config-group-taxonomy-option"><?php _e('Taxonomy', 'affilicious'); ?></label>
            <select id="aff-amazon-status-config-group-taxonomy-option" class="aff-import-config-group-option" name="taxonomy">
                <option value="none" <% if(taxonomy === null) { %> selected<% } %>><?php _e('No taxonomy', 'affilicious'); ?></option>
                <?php foreach ($taxonomies as $taxonomy): ?>
                    <option value="<?php echo esc_attr($taxonomy['name']); ?>"<% if(taxonomy === '<?php echo esc_attr($taxonomy['name']); ?>') { %> selected<% } %>><?php echo esc_html($taxonomy['label']); ?></option>
                <?php endforeach; ?>
            </select>

            <label class="aff-import-config-group-label">
                <input id="aff-amazon-status-config-group-term-option" class="regular-text" name="terms" placeholder="<?php _e('Enter terms...', 'affilicious'); ?>" <% if(taxonomy === null) { %> disabled<% } %> >
            </label>
        </div>
    </div>
</script>
