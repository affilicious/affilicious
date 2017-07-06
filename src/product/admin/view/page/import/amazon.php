<!--suppress HtmlUnknownTarget -->

<div class="aff-amazon-import">
    <form class="aff-amazon-import-search">
        <input class="aff-amazon-import-search-value" type="search" placeholder="Enter your search value...">

        <select class="aff-amazon-import-search-type">
            <option value="keyword">Keyword</option>
            <option value="keyword">ASIN</option>
            <option value="keyword">EAN</option>
        </select>
    </form>

    <div class="aff-amazon-import-results">
        <script class="aff-amazon-import-results-template" type="text/template">
            <div class="aff-amazon-import-results-item">
                <h3 class="aff-amazon-import-results-item-title"><%= name %> <%= slug %></h3>
                <% if(thumbnail) { %>
                    <img class="aff-amazon-import-results-item-thumbnail" src="<%= thumbnail.src %>">
                <% } %>
            </div>
        </script>
    </div>
</div>
