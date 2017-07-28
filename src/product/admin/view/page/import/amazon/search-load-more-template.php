<script id="aff-amazon-import-load-more-template" type="text/template">
    <% if(enabled) { %>
        <% if(!loading && !noResults && !error) { %>
            <button class="aff-amazon-import-load-more-button button delete">
                <?php _e('Load more', 'affilicious'); ?>
            </button>
        <% } %>

        <% if(loading && !noResults && !error) { %>
            <div class="aff-amazon-import-load-more-loading">
                <span class="aff-amazon-import-load-more-loading-spinner spinner is-active"></span>
            </div>
        <% } %>

        <% if(!loading && noResults && !error) { %>
            <p class="aff-amazon-import-load-more-no-results">
                <?php _e('No more results', 'affilicious'); ?>
            </p>
        <% } %>

        <% if(!loading && error) { %>
            <div class="aff-amazon-import-load-more-error aff-notice aff-error-notice" role="alert">
                <?php _e('The search has failed because an error has occurred.', 'affilicious'); ?>
            </div>
        <% } %>
    <% } %>
</script>
