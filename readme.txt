=== Affilicious ===
Contributors: affilicioustheme
Author URI: https://affilicioustheme.com
Plugin URI: https://affilicioustheme.com/downloads/affilicious
Requires at least: 4.5
Tested up to: 4.8.2
Stable tag: 0.9.8
Tags: Affiliate, SEO, Products
License: GPL-2.0
License URI: https://opensource.org/licenses/GPL-2.0

Create and manage affiliate websites in Wordpress with products & variants, automatic product updates, price comparisons, shops, details and more.

== Description ==

Affilicious is an affiliate platform build on top of Wordpress. It allows affiliate marketers to easily maintain websites with support for
product & variants, automatic product updates, price comparisons, details, attributes and more. Everything for free.

= Does this affect me as an affiliate marketer? =
Yes, it does. We are affiliate marketers ourselves and we know that this business can be challenging and expensive at the beginning.
The competition gets bigger and bigger. Having a great foundation for your websites is just essential!

= Why should I care as a developer? =
Really, there are many reasons! You as a developer can benefit from extending Affilicious by building custom plugins and themes.
Additionally, the affiliate marketing community is growing fast and needs many individual plugins to manage their websites. I'm sure that there are
many customers who can't wait to use your plugins and/or themes!

Maybe you are asking how complex it can be to develop something with Affilicious. Well, the answer is: Easy compared to other similar solutions.
You have to know that Affilicious is developed thinking about third party developers and theme authors needs from the beginning.
Here is why developing with the Affilicious Plugin is really fun:

1. Build on top of the [Wordpress](https://wordpress.com) platform.
2. Real [Object-Oriented Programming](https://en.wikipedia.org/wiki/Object-oriented_programming) with classes, interfaces and namespaces.
3. [Carbon Fields](https://carbonfields.net) and [Backbone](http://backbonejs.org) for building complex forms with ease.
4. [Pimple](http://pimple.sensiolabs.org) as a small Dependency Injection Container.
5. [NodeJS](https://nodejs.org) with [Gulp](http://gulpjs.com) for building assets.
6. [Sass](http://sass-lang.com) and [ECMAscript 6](https://babeljs.io/docs/learn-es2015/) Support.
7. Lots of build-in Wordpress [Hooks](https://codex.wordpress.org/Plugin_API/Hooks) and [Filters](https://codex.wordpress.org/Plugin_API/Filter_Reference).
8. [Composer](https://getcomposer.org) for easy vendor usage.
9. Automated tests with [PHPUnit](https://phpunit.de).

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

== Screenshots ==
1. This is the general Affilicious meta box. You can choose between simple products and complex products with variants.
2. Each product can have multiple shops with affiliate links, availabilities and prices. Each shop will be updated automatically.
3. Products without details are worth nothing. Define as many details you want.
4. Optionally, put a rating and some votes for your products.

== Changelog ==
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
