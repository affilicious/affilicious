<?php
    /** @var array $categories */
?>

<script id="aff-amazon-import-search-form-template" type="text/template">
    <div class="aff-panel">
        <div class="aff-import-search-form-main aff-panel-body">
            <label class="screen-reader-text" for="aff-amazon-import-search-term"><?php _e('Search term', 'affilicious'); ?></label>
            <input id="aff-amazon-import-search-term" class="aff-import-search-form-main-item aff-import-search-form-main-item-term" name="term" type="search" placeholder="<% if(type == 'keywords') { %><?php _e('Enter your search term...', 'affilicious'); ?><% } else { %><?php _e('Enter your ASIN...', 'affilicious'); ?><% } %>" value="<%= term %>">

            <button class="aff-import-search-form-main-item aff-import-search-form-main-item-submit button-primary" <% if(!providerConfigured) { %>disabled<% } %>>
                <?php _e('Search', 'affilicious'); ?>
            </button>
        </div>

        <div class="aff-import-search-form-filters aff-panel-footer">
            <label class="screen-reader-text" for="aff-amazon-import-search-type"><?php _e('Search type', 'affilicious'); ?></label>
            <select id="aff-amazon-import-search-type" class="aff-import-search-form-filters-item aff-import-search-form-filters-item-select" name="type">
                <option value="keywords"><?php _e('Keywords', 'affilicious'); ?></option>
                <option value="asin"><?php _e('ASIN', 'affilicious'); ?></option>
            </select>

            <% if(type == 'keywords') { %>
                <label class="screen-reader-text" for="aff-amazon-import-search-category"><?php _e('Search category', 'affilicious'); ?></label>
                <select id="aff-amazon-import-search-category" class="aff-import-search-form-filters-item aff-import-search-form-filters-item-select" name="category">
                    <?php foreach ($categories as $key => $name): ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($name); ?></option>
                    <?php endforeach; ?>
                </select>
            <% } %>

            <label class="screen-reader-text" for="aff-amazon-import-search-with-variants"><?php _e('Search with variants', 'affilicious'); ?></label>
            <select id="aff-amazon-import-search-with-variants" class="aff-import-search-form-filters-item aff-import-search-form-filters-item-select" name="with-variants">
                <option value="no"><?php _e('Without variants', 'affilicious'); ?></option>
                <option value="yes"><?php _e('With variants', 'affilicious'); ?></option>
            </select>

            <% if(type == 'keywords' && category == 'All' && withVariants == 'yes') { %>
                <div class="aff-import-search-form-filters-notice" role="alert">
                    <?php _e('Search with product variants doesn\'t work if the category "All" is selected.' , 'affilicious'); ?>
                </div>
            <% } else if(type == 'keywords' && category != 'All' && withVariants == 'yes') { %>
                <div class="aff-import-search-form-filters-notice" role="alert">
                    <?php _e('Due to limitations, not all products with variants are found in the keyword search.', 'affilicious'); ?>
                </div>
            <% } else if(type == 'asin' && withVariants == 'yes') { %>
                <div class="aff-import-search-form-filters-notice" role="alert">
                    <?php _e('Note that not all products with variants contain an image.' , 'affilicious'); ?>
                </div>
            <% } %>
        </div>
    </div>

    <% if(loading) { %>
        <div class="aff-import-search-form-loading">
            <span class="aff-import-search-form-loading-spinner spinner is-active"></span>
        </div>
    <% } else if(noResults) { %>
        <div class="aff-import-search-form-no-results aff-notice aff-warning-notice" role="alert">
            <% if(noResultsMessage) { %>
                <%= noResultsMessage %>
            <% } else { %>
                <?php _e('No results have been found for the search term.', 'affilicious-ebay-import-and-update'); ?>
            <% } %>
        </div>
    <% } else if(error) { %>
        <div class="aff-import-search-form-error aff-notice aff-error-notice" role="alert">
            <% if(errorMessage) { %>
                <%= errorMessage %>
            <% } else { %>
	            <?php _e('The search has failed because an error has occurred.', 'affilicious'); ?>
            <% } %>
        </div>
    <% } %>
</script>
