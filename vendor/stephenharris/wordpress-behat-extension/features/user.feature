Feature: Managing users
  In order to manage my editors
  As a blog administrator
  I need to be able to add and remove users and change their role

  Background:
    Given I have a vanilla wordpress installation
      | name              | email                   | username | password |
      | WordPress Testing | testing@example.invalid | admin    | password |
    And I am logged in as "admin" with password "password"

  @javascript
  Scenario: I can add a new user
    Given I go to menu item "Users"
    When I click on the "Add New" link in the header
    Then I should see "Add New User"

    When I fill in "user_login" with "neweditor"
    And I fill in "email" with "neweditor@example.org"
    And I fill in "first_name" with "Ed"
    And I fill in "last_name" with "New"
    And I select "editor" from "role"
    And I press "Add New User"
    Then I should see "New user created"

  @javascript
  Scenario: I can go straight to the Add New User screen
    Given I am on "/wp-admin/"
    When I go to menu item "Users > Add New"
    Then I should see "Add New User"
