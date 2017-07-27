<script id="aff-amazon-import-search-form-template" type="text/template">
    <div class="aff-amazon-import-search-form-panel">
        <div class="aff-amazon-import-search-form-panel-main">
            <label class="screen-reader-text" for="term"><?php _e('Search term', 'affilicious'); ?></label>
            <input class="aff-amazon-import-search-form-panel-main-item" name="term" type="search" placeholder="<% if(type == 'keywords') { %><?php _e('Enter your search term...', 'affilicious'); ?><% } else { %><?php _e('Enter your ASIN...', 'affilicious'); ?><% } %>" value="<%= term %>">

            <button class="aff-amazon-import-search-form-panel-main-item button-primary <% if(!term) { %>disabled<% } %>" <% if(!providerConfigured) { %>disabled="disabled"<% } %>><?php _e('Search', 'affilicious'); ?></button>
        </div>

        <div class="aff-amazon-import-search-form-panel-filters">
            <label class="screen-reader-text" for="type"><?php _e('Search type', 'affilicious'); ?></label>
            <select class="aff-amazon-import-search-form-panel-filters-option" name="type">
                <option value="keywords" <% if(type == 'keywords') { %>selected="selected"<% } %>><?php _e('Keywords', 'affilicious'); ?></option>
                <option value="asin" <% if(type == 'asin') { %>selected="selected"<% } %>><?php _e('ASIN', 'affilicious'); ?></option>
            </select>

            <label class="screen-reader-text" for="category"><?php _e('Search category', 'affilicious'); ?></label>
            <select class="aff-amazon-import-search-form-panel-filters-option" name="category">
                <option value="All" <% if(category == 'All') { %>selected="selected"<% } %>><?php _e('All Categories', 'affilicious'); ?></option>
                <option value="Apparel" <% if(category == 'Apparel') { %>selected="selected"<% } %>><?php _e('Apparel', 'affilicious'); ?></option>
                <option value="Appliances" <% if(category == 'Appliances') { %>selected="selected"<% } %>><?php _e('Appliances', 'affilicious'); ?></option>
                <option value="Automotive" <% if(category == 'Automotive') { %>selected="selected"<% } %>><?php _e('Automotive', 'affilicious'); ?></option>
                <option value="Baby" <% if(category == 'Baby') { %>selected="selected"<% } %>><?php _e('Baby', 'affilicious'); ?></option>
                <option value="Beauty" <% if(category == 'Beauty') { %>selected="selected"<% } %>><?php _e('Beauty', 'affilicious'); ?></option>
                <option value="Books" <% if(category == 'Books') { %>selected="selected"<% } %>><?php _e('Books', 'affilicious'); ?></option>
                <option value="DVD" <% if(category == 'DVD') { %>selected="selected"<% } %>><?php _e('DVD', 'affilicious'); ?></option>
                <option value="Electronics" <% if(category == 'Electronics') { %>selected="selected"<% } %>><?php _e('Electronics', 'affilicious'); ?></option>
                <option value="Furniture" <% if(category == 'Furniture') { %>selected="selected"<% } %>><?php _e('Furniture', 'affilicious'); ?></option>
                <option value="GiftCards" <% if(category == 'GiftCards') { %>selected="selected"<% } %>><?php _e('Gift Cards', 'affilicious'); ?></option>
                <option value="Grocery" <% if(category == 'Grocery') { %>selected="selected"<% } %>><?php _e('Grocery', 'affilicious'); ?></option>
                <option value="HealthPersonalCare" <% if(category == 'HealthPersonalCare') { %>selected="selected"<% } %>><?php _e('Health & Personal Care', 'affilicious'); ?></option>
                <option value="HomeGarden" <% if(category == 'HomeGarden') { %>selected="selected"<% } %>><?php _e('Home & Garden', 'affilicious'); ?></option>
                <option value="Industrial" <% if(category == 'Industrial') { %>selected="selected"<% } %>><?php _e('Industrial', 'affilicious'); ?></option>
                <option value="Jewelry" <% if(category == 'Jewelry') { %>selected="selected"<% } %>><?php _e('Jewelry', 'affilicious'); ?></option>
                <option value="KindleStore" <% if(category == 'KindleStore') { %>selected="selected"<% } %>><?php _e('KindleStore', 'affilicious'); ?></option>
                <option value="LawnAndGarden" <% if(category == 'LawnAndGarden') { %>selected="selected"<% } %>><?php _e('Lawn & Garden', 'affilicious'); ?></option>
                <option value="Luggage" <% if(category == 'Luggage') { %>selected="selected"<% } %>><?php _e('Luggage', 'affilicious'); ?></option>
                <option value="LuxuryBeauty" <% if(category == 'LuxuryBeauty') { %>selected="selected"<% } %>><?php _e('Luxury & Beauty', 'affilicious'); ?></option>
                <option value="Marketplace" <% if(category == 'Marketplace') { %>selected="selected"<% } %>><?php _e('Marketplace', 'affilicious'); ?></option>
                <option value="Music" <% if(category == 'Music') { %>selected="selected"<% } %>><?php _e('Music', 'affilicious'); ?></option>
                <option value="MusicalInstruments" <% if(category == 'MusicalInstruments') { %>selected="selected"<% } %>><?php _e('Musical Instruments', 'affilicious'); ?></option>
                <option value="OfficeProducts" <% if(category == 'OfficeProducts') { %>selected="selected"<% } %>><?php _e('Office Products', 'affilicious'); ?></option>
                <option value="Pantry" <% if(category == 'Pantry') { %>selected="selected"<% } %>><?php _e('Pantry', 'affilicious'); ?></option>
                <option value="PCHardware" <% if(category == 'PCHardware') { %>selected="selected"<% } %>><?php _e('PC Hardware', 'affilicious'); ?></option>
                <option value="PetSupplies" <% if(category == 'PetSupplies') { %>selected="selected"<% } %>><?php _e('Pet Supplies', 'affilicious'); ?></option>
                <option value="Shoes" <% if(category == 'Shoes') { %>selected="selected"<% } %>><?php _e('Shoes', 'affilicious'); ?></option>
                <option value="Software" <% if(category == 'Software') { %>selected="selected"<% } %>><?php _e('Software', 'affilicious'); ?></option>
                <option value="SportingGoods" <% if(category == 'SportingGoods') { %>selected="selected"<% } %>><?php _e('Sporting Goods', 'affilicious'); ?></option>
                <option value="Toys" <% if(category == 'Toys') { %>selected="selected"<% } %>><?php _e('Toys', 'affilicious'); ?></option>
                <option value="Video" <% if(category == 'Video') { %>selected="selected"<% } %>><?php _e('Video', 'affilicious'); ?></option>
                <option value="VideoGames" <% if(category == 'VideoGames') { %>selected="selected"<% } %>><?php _e('Video Games', 'affilicious'); ?></option>
                <option value="Watches" <% if(category == 'Watches') { %>selected="selected"<% } %>><?php _e('Watches', 'affilicious'); ?></option>
            </select>

            <label class="screen-reader-text" for="with-variants"><?php _e('Search with variants', 'affilicious'); ?></label>
            <select class="aff-amazon-import-search-form-panel-filters-option" name="with-variants">
                <option value="no" <% if(withVariants == 'no') { %>selected="selected"<% } %>><?php _e('Without variants', 'affilicious'); ?></option>
                <option value="yes" <% if(withVariants == 'yes') { %>selected="selected"<% } %>><?php _e('With variants', 'affilicious'); ?></option>
            </select>

            <% if(category == 'All' && withVariants == 'yes') { %>
                <div class="aff-amazon-import-search-form-panel-filters-notice">
                    <p><?php _e('Search with product variants doesn\'t work if the category "All" is selected.' , 'affilicious'); ?></p>
                </div>
            <% } %>
        </div>
    </div>

    <% if(loading) { %>
        <div class="aff-amazon-import-search-form-loading">
            <span class="aff-amazon-import-search-form-loading-spinner spinner is-active"></span>
        </div>
    <% } %>
</script>
