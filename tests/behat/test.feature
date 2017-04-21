Feature: Accessing WordPress site
  As a WordPress developer
  In order to know this Apache is serving static HTML
  I'd like to check the WordPress readme.html is visible

  @javascript
  Scenario: Visiting the homepage
    Given I am logged in as an admin
    And I am on the Dashboard
    Then I should see "Affilicious"
