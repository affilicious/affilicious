<script id="aff-amazon-import-search-results-item-template" type="text/template">
    <div class="aff-amazon-import-search-results-item" data-parent="<% if(typeof variants !== 'undefined' && variants !== null) { %>true<% } else { %>false<% } %>" <% if(typeof shops !== 'undefined' && shops !== null) { %>data-affiliate-product-id="<%= shops[0].tracking.affiliate_product_id %>"<% } %>>
        <div class="aff-amazon-import-search-results-item-content">
            <div class="aff-amazon-import-search-results-item-content-media">
                <% if(typeof thumbnail !== 'undefined' && thumbnail !== null) { %>
                    <div class="aff-amazon-import-search-results-item-thumbnail">
                        <img class="aff-amazon-import-search-results-item-thumbnail-image" src="<%= thumbnail.src %>">
                    </div>
                <% } %>
            </div>

            <div class="aff-amazon-import-search-results-item-content-main">
                <h1 class="aff-amazon-import-search-results-item-title"><%= name %></h1>

                <% if(typeof shops !== 'undefined' && shops !== null && shops[0].pricing.price !== null) { %>
                    <div class="aff-amazon-import-search-results-item-price">
                        <span class="aff-amazon-import-search-results-item-price-current">
                            <%= shops[0].pricing.price.value %> <%= shops[0].pricing.price.currency.symbol %>
                        </span>

                        <% if(shops[0].pricing.old_price) { %>
                            <span class="aff-amazon-import-search-results-item-price-old">
                                <%= shops[0].pricing.old_price.value %> <%= shops[0].pricing.old_price.currency.symbol %>
                            </span>
                        <% } %>
                    </div>
                <% } %>

                <% if(typeof shops !== 'undefined' && shops !== null && shops[0].tracking.affiliate_product_id !== null) { %>
                    <span class="aff-amazon-import-search-results-item-affiliate-product-id"><%= shops[0].tracking.affiliate_product_id %></span>
                <% } %>

                <% if(typeof variants !== 'undefined' && variants !== null) { %>
                    <div class="aff-amazon-import-search-results-item-variants">
                        <h3 class="aff-amazon-import-search-results-item-variants-title"><?php _e('Variants', 'affilicious'); ?></h3>

                        <% _.each(variants, function(variant, i) { %>
                            <% if(i == 3) { %>
                                <a class="aff-amazon-import-search-results-item-variants-show-all" href="#"><?php _e('Show all', 'affilicious'); ?> (+<%= this.length - 3 %>)</a>
                            <% } %>

                            <div class="aff-amazon-import-search-results-item-variants-item" <% if(typeof shops !== 'undefined' && shops !== null) { %>data-affiliate-product-id="<%= variant.shops[0].tracking.affiliate_product_id %>"<% } %> <% if(i >= 3) { %>style="display: none;"<% } %>>
                                <h2 class="aff-amazon-import-search-results-item-variants-item-title"><%= variant.name %></h2>

                                <% if(typeof variant.shops !== 'undefined' && variant.shops !== null && variant.shops[0].pricing.price !== null) { %>
                                    <div class="aff-amazon-import-search-results-item-variants-item-price">
                                        <span class="aff-amazon-import-search-results-item-variants-item-price-current">
                                            <%= variant.shops[0].pricing.price.value %> <%= variant.shops[0].pricing.price.currency.symbol %>
                                        </span>

                                        <% if(variant.shops[0].pricing.old_price !== null) { %>
                                            <span class="aff-amazon-import-search-results-item-variants-item-price-old">
                                                <%= variant.shops[0].pricing.old_price.value %> <%= variant.shops[0].pricing.old_price.currency.symbol %>
                                            </span>
                                        <% } %>
                                    </div>
                                <% } %>

                                <% if(typeof variant.attributes !== 'undefined' && variant.attributes !== null) { %>
                                    <ul class="aff-amazon-import-search-results-item-variants-item-attributes">
                                        <% _.each(variant.attributes, function(attribute) { %>
                                            <li class="aff-amazon-import-search-results-item-variants-item-attributes-item">
                                                <span class="aff-amazon-import-search-results-item-variants-item-attributes-item-name"><%= attribute.name %></span>
                                                <span class="aff-amazon-import-search-results-item-variants-item-attributes-item-value"><%= attribute.value %></span>
                                            </li>
                                        <% }); %>
                                    </ul>
                                <% } %>
                            </div>
                        <% }, variants); %>
                    </div>
                <% } %>
            </div>
        </div>

        <div class="aff-amazon-import-search-results-item-actions">
            <button class="aff-amazon-import-search-results-item-actions-import">
                <?php _e('Import', 'affilicious'); ?>
            </button>
        </div>
    </div>
</script>
