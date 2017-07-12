<script id="aff-amazon-import-load-more-template" type="text/template">
    <% if(!loading && !noResults) { %>
        <button class="aff-amazon-import-load-more-button button delete">
            <?php _e('Load more', 'affilicious'); ?>
        </button>
    <% } %>

    <% if(loading && !noResults) { %>
        <div class="aff-amazon-import-load-more-loading">
            <span class="aff-amazon-import-load-more-loading-spinner spinner is-active"></span>
        </div>
    <% } %>

    <% if(!loading && noResults) { %>
        <p class="aff-amazon-import-load-more-no-results">
            <?php _e('No more results', 'affilicious'); ?>
        </p>
    <% } %>
</script>
