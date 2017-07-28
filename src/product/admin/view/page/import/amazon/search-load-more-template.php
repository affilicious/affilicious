<script id="aff-amazon-import-load-more-template" type="text/template">
    <% if(enabled) { %>
        <% if(!loading && !noResults && !error) { %>
            <button class="aff-amazon-import-load-more-button button delete">
                <?php _e('Load more', 'affilicious'); ?>
            </button>
        <% } else if(loading) { %>
            <div class="aff-amazon-import-load-more-loading">
                <span class="aff-amazon-import-load-more-loading-spinner spinner is-active"></span>
            </div>
        <% } else if(!loading && noResults) { %>
            <p class="aff-amazon-import-load-more-no-results">
                <?php _e('No more results', 'affilicious'); ?>
            </p>
        <% } else if(!loading && error) { %>
            <div class="aff-amazon-import-load-more-error aff-notice aff-error-notice" role="alert">
                <% if(errorMessage) { %>
                    <%= errorMessage %>
                <% } else { %>
                    <?php _e('The search has failed because an error has occurred.', 'affilicious'); ?>
                <% } %>
            </div>
        <% } %>
    <% } %>
</script>
