<script id="aff-amazon-import-config-status-template" type="text/template">
    <div class="aff-import-config-group aff-panel">
        <div class="aff-import-config-group-header aff-panel-header">
            <h2 class="aff-panel-title"><?php _e('Status', 'affilicious'); ?></h2>
        </div>

        <div class="aff-import-config-group-body aff-panel-body">
            <label class="aff-import-config-group-label" for="publish">
                <input class="aff-import-config-group-option aff-amazon-import-config-group-option-publish" name="status" type="radio" value="publish"<% if(status == 'publish') { %> checked<% } %>>
                <?php _e('Publish product directly', 'affilicious'); ?>
            </label>

            <label class="aff-import-config-group-label" for="draft">
                <input class="aff-import-config-group-option aff-amazon-import-config-group-option-draft" name="status" type="radio" value="draft"<% if(status == 'draft') { %> checked<% } %>>
                <?php _e('Save product as draft', 'affilicious'); ?>
            </label>
        </div>
    </div>
</script>
