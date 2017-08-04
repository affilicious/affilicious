<script id="aff-amazon-import-search-form-template" type="text/template">
    <div class="aff-panel">
        <div class="aff-import-search-form-main aff-panel-body">
            <label class="screen-reader-text" for="aff-amazon-import-search-term"><?php _e('Search term', 'affilicious'); ?></label>
            <input id="aff-amazon-import-search-term" class="aff-import-search-form-main-item" name="term" type="search" placeholder="<% if(type == 'keywords') { %><?php _e('Enter your search term...', 'affilicious'); ?><% } else { %><?php _e('Enter your ASIN...', 'affilicious'); ?><% } %>" value="<%= term %>">

            <button class="aff-import-search-form-main-submit button-primary" <% if(!providerConfigured) { %>disabled<% } %>>
                <?php _e('Search', 'affilicious'); ?>
            </button>
        </div>

        <div class="aff-import-search-form-filters aff-panel-footer">
            <label class="screen-reader-text" for="aff-amazon-import-search-type"><?php _e('Search type', 'affilicious'); ?></label>
            <select id="aff-amazon-import-search-type" class="aff-import-search-form-filters-item" name="type">
                <option value="keywords"><?php _e('Keywords', 'affilicious'); ?></option>
                <option value="asin"><?php _e('ASIN', 'affilicious'); ?></option>
            </select>

            <label class="screen-reader-text" for="aff-amazon-import-search-category"><?php _e('Search category', 'affilicious'); ?></label>
            <select id="aff-amazon-import-search-category" class="aff-import-search-form-filters-item" name="category">
                <option value="All" selected><?php _e('All Categories', 'affilicious'); ?></option>
                <option value="Apparel"><?php _e('Apparel', 'affilicious'); ?></option>
                <option value="Appliances"><?php _e('Appliances', 'affilicious'); ?></option>
                <option value="Automotive"><?php _e('Automotive', 'affilicious'); ?></option>
                <option value="Baby"><?php _e('Baby', 'affilicious'); ?></option>
                <option value="Beauty"><?php _e('Beauty', 'affilicious'); ?></option>
                <option value="Books"><?php _e('Books', 'affilicious'); ?></option>
                <option value="DVD"><?php _e('DVD', 'affilicious'); ?></option>
                <option value="Electronics"><?php _e('Electronics', 'affilicious'); ?></option>
                <option value="Furniture"><?php _e('Furniture', 'affilicious'); ?></option>
                <option value="GiftCards"><?php _e('Gift Cards', 'affilicious'); ?></option>
                <option value="Grocery"><?php _e('Grocery', 'affilicious'); ?></option>
                <option value="HealthPersonalCare"><?php _e('Health & Personal Care', 'affilicious'); ?></option>
                <option value="HomeGarden"><?php _e('Home & Garden', 'affilicious'); ?></option>
                <option value="Industrial"><?php _e('Industrial', 'affilicious'); ?></option>
                <option value="Jewelry"><?php _e('Jewelry', 'affilicious'); ?></option>
                <option value="KindleStore"><?php _e('KindleStore', 'affilicious'); ?></option>
                <option value="LawnAndGarden"><?php _e('Lawn & Garden', 'affilicious'); ?></option>
                <option value="Luggage"><?php _e('Luggage', 'affilicious'); ?></option>
                <option value="LuxuryBeauty"><?php _e('Luxury & Beauty', 'affilicious'); ?></option>
                <option value="Marketplace"><?php _e('Marketplace', 'affilicious'); ?></option>
                <option value="Music"><?php _e('Music', 'affilicious'); ?></option>
                <option value="MusicalInstruments"><?php _e('Musical Instruments', 'affilicious'); ?></option>
                <option value="OfficeProducts"><?php _e('Office Products', 'affilicious'); ?></option>
                <option value="Pantry"><?php _e('Pantry', 'affilicious'); ?></option>
                <option value="PCHardware"><?php _e('PC Hardware', 'affilicious'); ?></option>
                <option value="PetSupplies"><?php _e('Pet Supplies', 'affilicious'); ?></option>
                <option value="Shoes"><?php _e('Shoes', 'affilicious'); ?></option>
                <option value="Software"><?php _e('Software', 'affilicious'); ?></option>
                <option value="SportingGoods"><?php _e('Sporting Goods', 'affilicious'); ?></option>
                <option value="Toys"><?php _e('Toys', 'affilicious'); ?></option>
                <option value="Video"><?php _e('Video', 'affilicious'); ?></option>
                <option value="VideoGames"><?php _e('Video Games', 'affilicious'); ?></option>
                <option value="Watches"><?php _e('Watches', 'affilicious'); ?></option>
            </select>

            <label class="screen-reader-text" for="aff-amazon-import-search-with-variants"><?php _e('Search with variants', 'affilicious'); ?></label>
            <select id="aff-amazon-import-search-with-variants" class="aff-import-search-form-filters-item" name="with-variants">
                <option value="no"><?php _e('Without variants', 'affilicious'); ?></option>
                <option value="yes"><?php _e('With variants', 'affilicious'); ?></option>
            </select>

            <% if(category == 'All' && withVariants == 'yes') { %>
                <div class="aff-import-search-form-filters-notice" role="alert">
                    <p><?php _e('Search with product variants doesn\'t work if the category "All" is selected.' , 'affilicious'); ?></p>
                </div>
            <% } %>
        </div>
    </div>

    <% if(loading) { %>
        <div class="aff-import-search-form-loading">
            <span class="aff-import-search-form-loading-spinner spinner is-active"></span>
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
