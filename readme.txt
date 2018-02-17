=== Affilicious ===
Contributors: affilicioustheme
Author URI: https://affilicioustheme.com
Plugin URI: https://affilicioustheme.com/downloads/affilicious
Requires at least: 4.5
Tested up to: 4.9.4
Stable tag: 0.9.22
Tags: Affiliate, SEO, Products
License: GPL-2.0
License URI: https://opensource.org/licenses/GPL-2.0

Create and manage affiliate websites in Wordpress with products & variants, automatic product updates, price comparisons, shops, details and more.

== Description ==

Affilicious is a free and complete Wordpress affiliate solution that allows you to sell affiliate products without effort. Learn more at [https://affilicioustheme.de](https://affilicioustheme.de/?utm_campaign=affilicious&utm_source=wordpress&utm_medium=description&utm_content=description-introduction) (Homepage is currently only available in German).

= Sell anything effortlessly =

Affilicious was build to make the life of affiliate marketers much easier. It allows you to sell any product from various platforms. Whether it's from Amazon, Ebay or from a custom source: Everything is possible!

= By far the easiest solution =

It took a lot of time and feedback for Affilicious to be the simplest solution on the market. Now, we can assure you that the simple interface with well structured options will ensure that you never want to use anything other than Affilicious. Just try it!

= The full control remains with you =

No matter if affiliate links, shops with prices or more: Affilicious gives you the full control about your affiliate store. Add and remove products or change the design as you like. Nobody will stop you!

= Localized in English and German =

Affilicious comes with support for two languages: English and German. However, you can always add your own language like any other Wordpress plugin or contribute to this project!

= Define your style with themes =

Choose from beautiful [affiliate themes](https://affilicioustheme.de/downloads/category/themes/?utm_campaign=themes&utm_source=wordpress&utm_medium=description&utm_content=description-themes) which seamlessly integrate Affilicious into their core:

- [Affilivice](https://affilicioustheme.de/downloads/affilivice/?utm_campaign=affilivice&utm_source=wordpress&utm_medium=description&utm_content=description-themes-affilivice) This free theme will give your Wordpress affiliate site a new lease of life.
- [Eleganz](https://affilicioustheme.de/downloads/eleganz/?utm_campaign=eleganz&utm_source=wordpress&utm_medium=description&utm_content=description-themes-eleganz) The fashion-adapted affiliate shop theme offers your visitors a suitable atmosphere to buy.

= Reach the next level with addons =

You can supercharge your affiliate store with [addons](https://affilicioustheme.de/downloads/category/erweiterungen/?utm_campaign=addons&utm_source=wordpress&utm_medium=description&utm_content=description-addons) for Affilicious like:

- [Alerts](https://affilicioustheme.de/downloads/alerts/?utm_campaign=alerts&utm_source=wordpress&utm_medium=description&utm_content=description-addons-alerts) Create meaningful alerts in just a few clicks that will help your visitors find what they are looking for.
- [Product Comparison](https://affilicioustheme.de/downloads/produktvergleich/?utm_campaign=product-comparison&utm_source=wordpress&utm_medium=description&utm_content=description-addons-product-comparison) Help your visitors make their purchasing decisions by including a clear comparison of products.
- [eBay Import And Update](https://affilicioustheme.de/downloads/ebay-import-und-update/?utm_campaign=ebay-import-and-update&utm_source=wordpress&utm_medium=description&utm_content=description-addons-ebay-import-and-update) Import and update your affiliate products easily from eBay.
- [Affilinet Import And Update](https://affilicioustheme.de/downloads/affilinet-import-und-update/?utm_campaign=affilinet-import-and-update&utm_source=wordpress&utm_medium=description&utm_content=description-addons-affilinet-import-and-update) Import and update your affiliate products with all shops directly from Affilinet.

= Build with developers in mind =

With Affilicious you will have a lot of fun as a developer. Flexible, extensible and Open Source - Affilicious is created with developers in mind right from the beginning. Contribute to [Github](https://github.com/affilicious-theme/affilicious)!

== Installation ==

=== From within WordPress ===
1. Visit 'Plugins > Add New'
1. Search for 'Affilicious'
1. Activate Affilicious from your current 'Plugins' page.
1. You're done!

=== Manually ===
1. Upload the `affilicious` folder to the `/wp-content/plugins/` directory
1. Activate the Affilicious plugin through the 'Plugins' menu in WordPress
1. You're done!

== Frequently Asked Questions ==

= Where can I find the documentation? =

The full documentation for Affilicious and all themes and plugin can be found at [here](https://affilicioustheme.zendesk.com/hc/de).

= Is there any free theme available? =

Yes, you can start with our free [Affilivice Theme](https://affilicioustheme.de/downloads/affilivice/?utm_campaign=affilivice&utm_source=wordpress&utm_medium=faq&utm_content=faq-free-theme-available).

= Does Affilicious work with my existing theme? =

Affilicious contains a feature called "Universal Mode" which allows you to run it on every Wordpress theme.

= Can I import existing products from Amazon, eBay and co.? =

No problem! The Amazon importer is already included in Affilicious. Other importers can be found as [addons](https://affilicioustheme.de/downloads/category/erweiterungen/?utm_campaign=addons&utm_source=wordpress&utm_medium=faq&utm_content=faq-import-existing-products) in our store.

= Do my products automatically stay up to date? =

Affilicious has a build-in updater which checks and applies product updates. You can choose the updates to recur once hourly, twice daily or daily.

= Where can I get support? =

If you want to get support, check out this [page](https://affilicioustheme.de/support?utm_campaign=support&utm_source=wordpress&utm_medium=faq&utm_content=faq-get-support).

== Screenshots ==
1. This is the general Affilicious meta box. You can choose between simple products and complex products with variants.
2. Each product can have multiple shops with affiliate links, availabilities and prices. Each shop will be updated automatically.
3. Products without details are worth nothing. Define as many details you want.
4. Optionally, put a rating and some votes for your products.

== Changelog ==
= 0.9.22 =
* New: Added product ID, price and availability column to product admin tables.
* New: Added daily logs cleaner to reduce logs records down to 10.000.
* New: Added daily orphaned product variants cleaner to clean up product variants without parent products.
* Improvement: Added Amazon ASIN search with variants.
* Improvement: Changed some translations.
* Fix: Fixed some translations.

= 0.9.21 =
* Improvement: Changed the product update flow to allow up to 100 product updates per provider.
* Improvement: Changed some log messages.
* Improvement: Added Amazon update worker as a service.
* Improvement: Removed some hooks.
* Improvement: Changed some Amazon worker configuration.

= 0.9.20 =
* Improvement: Added multisite support.
* Improvement: Added more info the system info.
* Improvement: Increased the logs preview to 100 entries.
* Improvement: Only the first shop of the default variant will be used for complex product updates now.
* Improvement: Improved Wordpress admin speed in some cases.
* Improvement: Changed the update semaphore counter behavior for hourly, twice daily and daily updates.

= 0.9.19 =
* Fix: Added support for logs table creation for multisites.

= 0.9.18 =
* New: Added possibility to download system info as txt.
* New: Added possibility to logs as txt.
* New: Added a new logs table to store the logs.
* New: Added a new tab in the preferences to show the logs.
* Improvement: Added some logging for the Amazon updates.
* Improvement: Added some more info to the system info.
* Improvement: Refactored and cleaned up some functions.
* Improvement: Added some more system info.
* Improvement: Moved some template like system info and logs into a different location.
* Improvement: Added UTM parameters to the link below the licenses.
* Fix: Fixed the product tags creation in the admin in some cases.
* Fix: Fixed the votes showing "0" on empty value.

= 0.9.17 =
* Improvement: readme.txt has been rewritten.
* Improvement: Optimized the Amazon update worker for throttled API requests.
* Improvement: Made the UTM parameters for download recommendation and addons page more dynamic.
* Improvement: Changed UTM parameters for plugin actions.
* Fix: Fixed the broken product updates in some Wordpress installations.

= 0.9.16 =
* New: Added possibility to add product term in the import.
* New: Custom product taxonomies have REST support now.
* New: Added possibility to add multiple terms in the Amazon import.
* New: Added download recommendation notices.
* New: Added plugin actions.
* Improvement: Added better import success and error highlighting.
* Improvement: Reduced the Amazon error rate caused by throttling.
* Improvement: Improved translations
* Improvement: Some marketing related links are tracked with UTM parameters now.
* Improvement: Refactored repository methods.
* Fix: Fixed the canonical setup on empty product archives.

= 0.9.15 =
* New: Added Amazon search fields like min price, max price, order and condition.
* New: Added retry button for Amazon import.
* New: Added indicator for already imported products.
* Improvement: Changed the default product status in the Amazon import.
* Improvement: Improved Amazon search and import error messages.
* Improvement: Made the search item thumbnails smaller.
* Fix: Removed the "explicit double search button" click in the Amazon import.

= 0.9.14 =
* New: Added aff_has_product_details.
* New: Added aff_get_shop_availability.
* Improvement: Added Amazon import message for no search results.
* Improvement: Added some translations.
* Fix: Fixed aff_get_shop_availability.

= 0.9.13 =
* Improvement: Optimized the template path system.
* Fix: Fixed a bug for the related product images in universal mode.
* Fix: Fixed Travis tests.
* Fix: Fixed some translations.

= 0.9.12 =
New: Added a start to the addons menu item.
New: Added universal box image gallery controls.
Improvement: Added "no details" message to the universal box
Improvement: Added support for units in the customizer.
Improvement: Optimized Amazon access and secret key description.
Improvement: Added PHP mbstring extensions check on installation.
Improvement: Added better support for the universal box in older browsers.
Improvement: Optimized the update manager.
Fix: Fixed the order of related products and accessories.
Fix: Added some fixes for related products in the universal box.
Fix: Fixed some product meta box warning notices.

= 0.9.11 =
New: Added log support.
Improvement: Added semaphore to prevent parallel product updates.
Fix: Added prevention for duplicate images which were created during the product updates.

= 0.9.10 =
New: Added universal mode.
New: Added thumbnails to product admin table.

= 0.9.9 =
New: Added system info for support reasons.
Improvement: Added license key daily checks.
Improvement: Optimized Wordpress tests.
Improvement: Outsourced the product attribute choices into a separate template.
Improvement: Optimized the image downloads.
Improvement: Optimized the Amazon update worker.
Fix: Fixed the Amazon product old price update and import in some cases.
Fix: Fixed the missing product archive pages on plugin reinstall.
Fix: Fixed the timestamp of newly created products and shops.

= 0.9.8 =
New: Added possibility to update Amazon affiliate links.
New: Added product archive template to taxonomies hierarchy.
Fix: Fixed the broken Amazon updates.

= 0.9.7 =
* New: Added provider type
* Improvement: Added some methods to the product factories.
* Fix: Fixed the new shop creation in the Amazon import.
* Fix: Fixed the key generator for plain numbers.
* Fix: Fixed and optimized the Amazon search and imports with variants.
* Fix: Various bug fixes.

= 0.9.6 =
* Fix: Fixed some options of the Amazon import.

= 0.9.5 =
* New: New templating system for HTML templates.
* Fix: Minor style fixes.

= 0.9.4 =
* New: Added the possibility to add custom import pages.
* Improvement: Added some translations.
* Improvement: Restructured the import assets modules.
* Improvement: Optimized the Amazon import.
* Improvement: Optimized functions output.

= 0.9.3 =
* Improvement: Removed the required inputs fields from the Amazon provider options.
* Improvement: Optimized some hook names.
* Improvement: Renamed „product“ options to „products“ options.
* Fix: Fixed some wrong translations.

= 0.9.2 =
* Improvement: Marked Webmozart as deprectated and introducted custom Wordpress friendly assert helper.
* Improvement: Switched PHPUnit to 5.7.
* Improvement: Optimized the custom product taxonomies options.
* Improvement: Wrote some unit tests.
* Improvement: Added product listeners for deleted and edited shop templates.
* Improvement: Added product listeners for deleted and edited attribute templates.
* Improvement: Added product listeners for deleted and edited detail templates.
* Fix: Fixed the missing products with draft status in the Amazon import.

= 0.9.1 =
* New: Added "aff_hooks" and "aff_admin_hooks" hooks.
* Improvement: Cleaned up the hooks and changed some priorities.
* Improvement: Changed the admin licences tab label.
* Improvement: Restructured some setups.
* Improvement: Cleaned up the dependency injection services.
* Improvement: Added version and min PHP version constants.
* Fix: Improved loading performance by removing missing public styles and scripts.
* Fix: Fixed the admin licenses box layout.
* Fix: Removed a typo in the Amazon import search form.

= 0.9 =
* New: Added Amazon import for simple and complex products with variants.
* New: Added some more functions for theme developers.
* New: Added some more hooks for theme und plugin developers.
* New: Added support for international Amazon product import and update usage.
* New: Added Amazon product update support for images.
* Improvement: Added selectize.js support for carbon input fields.
* Improvement: Added auto-completion for details and attributes tags in the products.
* Improvement: Renamed "Affiliate ID" to "Affiliate Product ID".
* Improvement: Added output formatter for the functions.
* Improvement: Increased the shops and variants limits.
* Improvement: Improved the function and method doc blocks.
* Improvement: Switched from "Image IDs" to "Images".
* Improvement: Restructured Gulp for handling Ecmascript 6 much better.
* Improvement: Added translations for error messages to display them in the front-end.
* Fix: Fixed many different bugs.

= 0.8.20 =
* Improvement: Product variants take the taxonomy terms of the parent complex product now.
* Fix: Fixed the status change of the product variants when the parent product status changed.

= 0.8.19 =
* Improvement: Moved the new banners, icons and screenshots to the SVN assets directory.

= 0.8.18 =
* Fix: Added a migration to fix the broken product slugs.

= 0.8.17 =
* New: Added 'aff_get_product_taxonomies' function
* Improvement: Made some hooks priority adjustments

= 0.8.16 =
* Fix: Fixed the boolean detail values after the automatic product update.

= 0.8.15 =
* Improvement: Whitespaces are removed from licenses.
* Fix: Fixed the value of boolean details.

= 0.8.14 =
* Fix: Removed the "noopener noreferrer" attribute from links
* Fix: Removed the disappearing licences.
* Fix: Fixed some functions for complex products usage.

= 0.8.13 =
* Improvement: Added some missing translations.

= 0.8.12 =
* New: Added license page for extensions and themes.
* New: Added Behat for automated functional testing.
* Improvement: Allowed decimal numbers in details.
* Improvement: Optimized the software code.
* Fix: Fixed Amazon product update.

= 0.8.11 =
* Improvement: Made Affilicious more error tolerant.
* Improvement: Changed the product menu name to the plural form.
* Improvement: Escaped some output to improve security.
* Improvement: Optimized developer function doc blocks.
* Fix: Fixed the broken product affiliate link in some cases.

= 0.8.10 =
* Fix: Fixed the broken complex products on missing attribute templates.

= 0.8.9 =
* New: Added boolean type for details.
* New: Added basic rest support for products.
* Improvement: Added some more functions for the front end.
* Improvement: Votes can contain 0 as value now.
* Improvement: Improved the model helpers.
* Fix: Fixed the Amazon updates in some cases.
* Fix: Fixed the product equality.

= 0.8.8 =
* Fix: Fixed the broken product relations.
* Fix: Added missing detail template ID.
* Fix: Fixed the product rating with „half values“.

= 0.8.7 =
* Improvement: Optimized thumbnails for invalid values.
* Improvement: Optimized readme's for new Github repository URI.
* Fix: Fixed some spelling.

= 0.8.6 =
* Fix: Fixed the missing product variants.

= 0.8.5 =
* Improvement: Removed the shop, attribute and detail template columns from the product admin table.
* Fix: Fixed the broken image, file and image gallery carbon buttons styles

= 0.8.4 =
* New: Added support for wordpress.org
* Improvement: Removed the custom EDD Updater
* Fix: Fixed the product relations
* Fix: Fixed the product variants post status
* Fix: Product variants are deleted like the complex parent products now.

= 0.8.3 =
* New: Added an option to set an update interval for old prices
* New: Added the "affilicious_init" and "affilicious_admin_init" Wordpress hooks.
* Fix: Fixed some annoying validation

= 0.8.2 =
* New: Added a method in code to get the model terms and posts.
* Fix: Fixed a bug related to empty content
* Fix: Optimized the custom scripts help text

= 0.8.1 =
* Fix: Optimized the custom header and footer scripts

= 0.8 =
* New: Beta Release
* New: Added an image gallery to the product variants
* Improvement: Optimized the shop workflow
* Improvement: Optimized the detail workflow
* Improvement: Optimized the attribute workflow
* Fix: Bug fixes

= 0.7.2 =
* Fix: Added some update bug fixes.

= 0.6 =
* New: Added support for product variants and attributes

= 0.5.2 =
* New: Added support for change logs
* New: Added Gulp for easier and faster development
* Improvement: Many optimizations and cleanups
