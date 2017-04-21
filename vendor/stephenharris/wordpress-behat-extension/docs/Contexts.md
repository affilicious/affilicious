# Contexts

This extension provides the following contexts.

Each must be manually specified in your `behat.yml` if you wish to use them.


## Core context

| Context          | description                                                   |
|------------------|---------------------------------------------------------------|
| WordPressContext | The base context. Used for setting up the WordPress database. |


## Admin/Login UI Contexts

These contexts deal with navigating around the WordPress admin and interacting with admin pages.

| Context                  | description                                                                       |
|--------------------------|-----------------------------------------------------------------------------------|
| WordPressAdminContext    | Navigating the WordPress admin, and asserting the current admin page being viewed |
| WordPressEditPostContext | Steps relating to the "Edit Post" (or any post type) page                         |
| WordPressLoginContext    | Steps relating to the log-in page                                                 |
| WordPressMailContext     | Steps relating to asserting the reciept of emails                                 |
| WordPressPostListContext | Steps relating to the 'posts' admin page (or any post type)                       |


## "Entity" Contexts

These contexts contain steps which deal with posts, terms and users etc in the abstract. Steps are typically `@Given` steps, to
create the entities, and `@Then` steps, asserting their existence or properties.

| Context                        | description                                       |
|--------------------------------|---------------------------------------------------|
| PostTypes\WordPressPostContext | Steps relating to posts in the database           |
| Terms\WordPressTermContext     | Steps relating to terms in the database           |
| Users\WordPressTermContext     | Steps relating to terms in the database           |
| Plugins\WordPressPluginContext | Steps relating to activating/deactivating plugins |
| Options\WordPressOptionContext | Steps relating to configuring settings            |