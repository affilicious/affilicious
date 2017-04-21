WordPress Extension for Behat 3
===============================

This is a Behat 3.0 Extension for WordPress plugin and theme development. 
You can use it to test your WordPress installation, or just test your plugin/theme without installing them in a normal WordPress installation (i.e. stand-alone).
The Extension allows you to use WordPress functions in your context class (if you extend it from Johnbillion\WordPressExtension\Context\WordPressContext).

Installation
------------

1. Add a composer development requirement for your WordPress theme or plugin:

    ```json
    {
        "require-dev" : {
            "johnbillion/wordpress-behat-extension": "~0.1",
            "johnpbloch/wordpress": "~4.0.0"
        }
    }
    ```

2. Add the following Behat configuration file:

    ```yml
    default:
      suites:
        default:
          contexts:
            - Johnbillion\WordPressExtension\Context\WordPressContext
      extensions:
        Johnbillion\WordPressExtension:
          path: '%paths.base/vendor/wordpress'
    
        Behat\MinkExtension:
          base_url:    'http://localhost:8000'
          sessions:
            default:
              goutte: ~
    
    ```

3. Install the vendors and initialize behat test suites

    ```bash
    composer update
    vendor/bin/behat --init
    ```

4. Start your development web server and point its document root to the wordpress directory in vendors (without mail function)

    ```bash
    php -S localhost:8000 -t vendor/wordpress -d disable_functions=mail
    ```

5. Write some Behat features and test them

    ```
    Feature: Manage plugins
        In order to manage plugins
        As an admin
        I need to enable and disable plugins
    
        Background:
            Given I have a vanilla wordpress installation
                | name          | email                   | username | password |
                | BDD WordPress | your@email.com          | admin    | test     |
            And I am logged in as "admin" with password "test"
    
        Scenario: Enable the dolly plugin
            Given there are plugins
                | plugin    | status  |
                | hello.php | enabled |
            When I go to "/wp-admin/"
            Then I should see a "#dolly" element
    
        Scenario: Disable the dolly plugin
            Given there are plugins
                | plugin    | status   |
                | hello.php | disabled |
            When I go to "/wp-admin/"
            Then I should not see a "#dolly" element
    
    ```

6. Run the tests

    ```bash
    vendor/bin/behat
    ```
