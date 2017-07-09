<!--suppress HtmlUnknownTarget -->

<div class="aff-amazon-import">
    <div class="aff-amazon-import-content">
        <script class="aff-amazon-import-search-template" type="text/template">
            <div class="aff-amazon-import-search-content">
                <label class="aff-amazon-import-search-term-label aff-amazon-import-search-label" for="term">
                    <input class="aff-amazon-import-search-term" name="term" type="search" placeholder="<?php _e('Enter your search term...', 'affilicious'); ?>" value="<%= term %>">
                </label>

                <label class="aff-amazon-import-search-type-label aff-amazon-import-search-label" for="type">
                    <select class="aff-amazon-import-search-type" name="type">
                        <option value="keywords" <% if(type == 'keywords') { %>selected="selected"<% } %>><?php _e('Keywords', 'affilicious'); ?></option>
                        <option value="asin" <% if(type == 'asin') { %>selected="selected"<% } %>><?php _e('ASIN', 'affilicious'); ?></option>
                    </select>
                </label>

                <label class="aff-amazon-import-search-category-label aff-amazon-import-search-label" for="category">
                    <select class="aff-amazon-import-search-category" name="category">
                        <option value="all" <% if(category == 'all') { %>selected="selected"<% } %>><?php _e('All', 'affilicious'); ?></option>
                        <option value="books" <% if(category == 'books') { %>selected="selected"<% } %>><?php _e('Books', 'affilicious'); ?></option>
                        <option value="dvd" <% if(category == 'dvd') { %>selected="selected"<% } %>><?php _e('DVD', 'affilicious'); ?></option>
                        <option value="music" <% if(category == 'music') { %>selected="selected"<% } %>><?php _e('Music', 'affilicious'); ?></option>
                        <option value="apparel" <% if(category == 'apparel') { %>selected="selected"<% } %>><?php _e('Apparel', 'affilicious'); ?></option>
                        <option value="video" <% if(category == 'video') { %>selected="selected"<% } %>><?php _e('Video', 'affilicious'); ?></option>
                        <option value="jewelry" <% if(category == 'jewelry') { %>selected="selected"<% } %>><?php _e('Jewelry', 'affilicious'); ?></option>
                        <option value="automotive" <% if(category == 'automotive') { %>selected="selected"<% } %>><?php _e('Automotive', 'affilicious'); ?></option>
                        <option value="watch" <% if(category == 'watch') { %>selected="selected"<% } %>><?php _e('Watch', 'affilicious'); ?></option>
                        <option value="electronics" <% if(category == 'electronics') { %>selected="selected"<% } %>><?php _e('Electronics', 'affilicious'); ?></option>
                    </select>
                </label>

                <button class="aff-amazon-import-search-submit button-primary" type="submit"><?php _e('Search', 'affilicious'); ?></button>
            </div>

            <% if(category == 'all') { %>
                <div class="aff-amazon-import-search-notice">
                    <p><?php _e('Search with product variants doesn\'t work if the category "All" is selected.' , 'affilicious'); ?></p>
                </div>
            <% } %>
        </script>

        <form class="aff-amazon-import-search"></form>

        <div class="aff-amazon-import-results">
            <script class="aff-amazon-import-results-template" type="text/template">
                <article class="aff-amazon-import-results-item" data-parent="<% if(typeof variants !== 'undefined' && variants !== null) { %>true<% } else { %>false<% } %>" <% if(typeof shops !== 'undefined' && shops !== null) { %>data-affiliate-product-id="<%= shops[0].tracking.affiliate_product_id %>"<% } %>>
                    <div class="aff-amazon-import-results-item-media">
                        <% if(typeof thumbnail !== 'undefined' && thumbnail !== null) { %>
                            <div class="aff-amazon-import-results-item-thumbnail">
                                <img class="aff-amazon-import-results-item-thumbnail-image" src="<%= thumbnail.src %>">
                            </div>
                        <% } %>
                    </div>

                    <div class="aff-amazon-import-results-item-content">
                        <h1 class="aff-amazon-import-results-item-title"><%= name %></h1>

                        <% if(typeof shops !== 'undefined' && shops !== null && shops[0].pricing.price !== null) { %>
                            <div class="aff-amazon-import-results-item-price">
                                <span class="aff-amazon-import-results-item-price-current">
                                    <%= shops[0].pricing.price.value %> <%= shops[0].pricing.price.currency.symbol %>
                                </span>

                                <% if(shops[0].pricing.old_price) { %>
                                    <span class="aff-amazon-import-results-item-price-old">
                                        <%= shops[0].pricing.old_price.value %> <%= shops[0].pricing.old_price.currency.symbol %>
                                    </span>
                                <% } %>
                            </div>
                        <% } %>

                        <% if(typeof variants !== 'undefined' && variants !== null) { %>
                            <div class="aff-amazon-import-results-item-variants">
                                <h3 class="aff-amazon-import-results-item-variants-title"><?php _e('Variants', 'affilicious'); ?></h3>
                                <% _.each(variants, function(variant, i) { %>
                                    <% if(i == 3) { %>
                                        <a class="aff-amazon-import-results-item-variants-show-all" href="#"><?php _e('Show all', 'affilicious'); ?></a>
                                    <% } %>

                                    <div class="aff-amazon-import-results-item-variants-item <% if(i > 2) { %>aff-amazon-import-results-item-variants-item-hidden<% } %>" <% if(typeof shops !== 'undefined' && shops !== null) { %>data-affiliate-product-id="<%= variant.shops[0].tracking.affiliate_product_id %>"<% } %>>
                                        <h2 class="aff-amazon-import-results-item-variants-item-title"><%= variant.name %></h2>

                                        <% if(typeof variant.shops !== 'undefined' && variant.shops !== null && variant.shops[0].pricing.price !== null) { %>
                                            <div class="aff-amazon-import-results-item-variants-item-price">
                                                <span class="aff-amazon-import-results-item-variants-item-price-current">
                                                    <%= variant.shops[0].pricing.price.value %> <%= variant.shops[0].pricing.price.currency.symbol %>
                                                </span>

                                                <% if(variant.shops[0].pricing.old_price !== null) { %>
                                                    <span class="aff-amazon-import-results-item-variants-item-price-old">
                                                        <%= variant.shops[0].pricing.old_price.value %> <%= variant.shops[0].pricing.old_price.currency.symbol %>
                                                    </span>
                                                <% } %>
                                            </div>
                                        <% } %>

                                        <% if(typeof variant.attributes !== 'undefined' && variant.attributes !== null) { %>
                                            <ul class="aff-amazon-import-results-item-variants-item-attributes">
                                                <% _.each(variant.attributes, function(attribute) { %>
                                                    <li class="aff-amazon-import-results-item-variants-item-attributes-item">
                                                        <span class="aff-amazon-import-results-item-variants-item-attributes-item-name"><%= attribute.name %></span>
                                                        <span class="aff-amazon-import-results-item-variants-item-attributes-item-value"><%= attribute.value %></span>
                                                    </li>
                                                <% }); %>
                                            </ul>
                                        <% } %>
                                    </div>
                                <% }); %>
                            </div>
                        <% } %>

                        <div class="aff-amazon-import-results-item-actions">
                            <button class="aff-amazon-import-results-item-actions-import">
                                <?php _e('Import', 'affilicious'); ?>
                            </button>
                        </div>
                    </div>
                </article>
            </script>
        </div>
    </div>
</div>

<!--<span class="spinner is-active"></span>-->
