Feature: Login
    In order to login into my WP website
    As an user
    I need to know my credentials

    Background:
        Given I have a vanilla wordpress installation
            | name          | email                   | username | password |
            | BDD WordPress | walter.dalmut@gmail.com | admin    | test     |

    Scenario: A valid user access to the platform
        When I am on "/wp-login.php"
        And I fill in "user_login" with "admin"
        And I fill in "pwd" with "test"
        And I press "Log In"
        Then I should be on "/wp-admin/"
        And I should see "Howdy, admin"

    Scenario: An existing user tries to login with a wrong password
        When I am on "/wp-login.php"
        And I fill in "user_login" with "admin"
        And I fill in "pwd" with "password"
        And I press "Log In"
        Then I should be on "/wp-login.php"
        And I should see "ERROR: The password you entered for the username admin is incorrect."

    Scenario: A nonexistent user tries to login
        When I am on "/wp-login.php"
        And I fill in "user_login" with "john"
        And I fill in "pwd" with "password"
        And I press "Log In"
        Then I should be on "/wp-login.php"
        And I should see "ERROR: Invalid username."



