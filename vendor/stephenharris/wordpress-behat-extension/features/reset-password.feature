Feature: Resetting a password
  As a WordPress developer
  In order to demonstrate email testing
  I'd like to reset the admin user's password

  Background:
    Given I have a vanilla wordpress installation
      | name              | email                   | username | password        |
      | WordPress Testing | testing@example.invalid | admin    | initialpassword |

  @javascript @insulated
  Scenario: Accessing admin initially and being redirected
    When I go to "/wp-admin/"
    Then I should be on the log-in page

  @javascript @insulated
  Scenario: Receiving a password reset email
    Given I am on the log-in page
    And I follow "Lost your password?"
    And I fill in "Username or Email" with "testing@example.invalid"
    And I press "Get New Password"
    Then I should see "Check your email for the confirmation link"
    And the latest email to testing@example.invalid should contain "Someone has requested a password reset for the following account"
    Given I follow the second URL in the latest email to testing@example.invalid
    And I fill in "pass1-text" with "newpassword"
    And I press "Reset Password"
    And I follow "Log in"
    And I fill in "user_login" with "admin"
    And I fill in "user_pass" with "newpassword"
    And I press "Log In"
    Then I should be on the "Dashboard" page
    And I should see "At a Glance"
