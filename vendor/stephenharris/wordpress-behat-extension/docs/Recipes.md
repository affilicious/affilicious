# Recipes

Since end-to-end testing requires a WordPress install to run the tests, setting up these tests
is not as straightforward as unit testing. This page contains a list of 'recipes' - some quick 
start guides on getting these tests set up in your development envrironment or CI infrastructure.

The general rule is:

 - List Behat, Mink, and your choice of driver(s) (recommended: Goutte and Selenium) as (composer) dependencies of your project
 - Install and set-up a WordPress instance (including database)
 - Install your project, (and the above dependencies) alongside your WordPress instance
 - Configure your Behat.yml with the database credentials and site URL
 - Install and run Selenium (if required)
 - Run the tests
 
WordPressBehatExtension is relatively flexible in that you can run it against *any* WordPress install. But you should
be aware that it will **empty your database tables of ALL data**. 

- [Travis CI](Recipes/Travis.md)
- Jenkins CI (*TODO*)
- VVV (*TODO*)

If you wish to add a recipe to this please submit a [pull request](https://github.com/stephenharris/WordPressBehatExtension/pulls).
 