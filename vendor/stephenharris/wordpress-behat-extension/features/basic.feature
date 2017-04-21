Feature: Accessing WordPress site
  As a WordPress developer
  In order to know this Travis thing is working
  I'd like to check the WordPress homepage is visible

  Background:
    Given I have a vanilla wordpress installation
      | name              | email                   | username | password        |
      | WordPress Testing | testing@example.invalid | admin    | initialpassword |

  @javascript @insulated
  Scenario: Visiting the homepage
    Given I am on "/"
    Then I should see "Proudly powered by WordPress"