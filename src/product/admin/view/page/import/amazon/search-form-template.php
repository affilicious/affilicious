<script id="aff-amazon-import-search-form-template" type="text/template">
    <div class="aff-amazon-import-search-form-content">
        <label class="aff-amazon-import-search-form-term-label aff-amazon-import-search-form-label" for="term">
            <input class="aff-amazon-import-search-form-term" name="term" type="search" placeholder="<?php _e('Enter your search term...', 'affilicious'); ?>" value="<%= term %>">
        </label>

        <label class="aff-amazon-import-search-form-type-label aff-amazon-import-search-form-label" for="type">
            <select class="aff-amazon-import-search-form-type" name="type">
                <option value="keywords" <% if(type == 'keywords') { %>selected="selected"<% } %>><?php _e('Keywords', 'affilicious'); ?></option>
                <option value="asin" <% if(type == 'asin') { %>selected="selected"<% } %>><?php _e('ASIN', 'affilicious'); ?></option>
            </select>
        </label>

        <label class="aff-amazon-import-search-form-category-label aff-amazon-import-search-form-label" for="category">
            <select class="aff-amazon-import-search-form-category" name="category">
                <option value="All" <% if(category == 'all') { %>selected="selected"<% } %>><?php _e('All', 'affilicious'); ?></option>
                <option value="Books" <% if(category == 'books') { %>selected="selected"<% } %>><?php _e('Books', 'affilicious'); ?></option>
                <option value="DVD" <% if(category == 'dvd') { %>selected="selected"<% } %>><?php _e('DVD', 'affilicious'); ?></option>
                <option value="Music" <% if(category == 'music') { %>selected="selected"<% } %>><?php _e('Music', 'affilicious'); ?></option>
                <option value="Apparel" <% if(category == 'apparel') { %>selected="selected"<% } %>><?php _e('Apparel', 'affilicious'); ?></option>
                <option value="Video" <% if(category == 'video') { %>selected="selected"<% } %>><?php _e('Video', 'affilicious'); ?></option>
                <option value="Jewelry" <% if(category == 'jewelry') { %>selected="selected"<% } %>><?php _e('Jewelry', 'affilicious'); ?></option>
                <option value="Automotive" <% if(category == 'automotive') { %>selected="selected"<% } %>><?php _e('Automotive', 'affilicious'); ?></option>
                <option value="Watch" <% if(category == 'watch') { %>selected="selected"<% } %>><?php _e('Watch', 'affilicious'); ?></option>
                <option value="Electronics" <% if(category == 'electronics') { %>selected="selected"<% } %>><?php _e('Electronics', 'affilicious'); ?></option>
            </select>
        </label>

        <button class="aff-amazon-import-search-form-submit button-primary" type="submit" <% if(!providerConfigured) { %>disabled="disabled"<% } %>><?php _e('Search', 'affilicious'); ?></button>
    </div>

    <% if(category == 'all') { %>
        <div class="aff-amazon-import-search-form-notice">
            <p><?php _e('Search with product variants doesn\'t work if the category "All" is selected.' , 'affilicious'); ?></p>
        </div>
    <% } %>

    <% if(loading) { %>
        <div class="aff-amazon-import-search-form-loading">
            <span class="aff-amazon-import-search-form-loading-spinner spinner is-active"></span>
        </div>
    <% } %>
</script>
