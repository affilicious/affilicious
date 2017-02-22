=== Affilicious ===
Contributors: affilicious
Author URI: https://affilicious.de
Plugin URI: https://affilicious.de
Requires at least: 4.5
Tested up to: 4.7
Stable tag: 0.8.4
Tags: Affiliate Marketing, SEO, Products
License: GPL-2.0
License URI: https://opensource.org/licenses/GPL-2.0

Create and manage affiliate websites in Wordpress with products & variants, automatic product updates, price comparisons, shops, details and more.

== Description ==

Affilicious is an Affiliate platform build on top of Wordpress. It allows affiliate marketers to easily maintain websites with support for
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
= 0.8.5 =
* Improvement: Removed the shop, attribute and detail template columns from the product admin table.
* Fix: Fixed the broken image, file and image gallery carbon buttons styles

= 0.8.4 =
* New: Added support for wordpress.org
* Improvement: Removed the custom EDD Updater
* Improvement: Cleaned up some product repository code
* Fix: Fixed the product relations
* Fix: Fixed the product variants post status
* Fix: Product variants are deleted like the complex parent products now.
* Fix: Fixed the complex product listener

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
