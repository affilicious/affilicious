Feature: admin menu

  Background:
    Given I have a vanilla wordpress installation
      | name          | email             | username | password |
      | BDD WordPress | admin@example.com | admin    | password |
    And I am logged in as "admin" with password "password"


  Scenario: I can view the post list
    When I go to "/wp-admin/index.php"
    And the admin menu should appear as
      | Dashboard  |
      | Posts      |
      | Media      |
      | Pages      |
      | Comments   |
      | Appearance |
      | Plugins    |
      | Users      |
      | Tools      |
      | Settings   |

  @javascript
  Scenario: I can view the post list with javascript enabled
    When I go to "/wp-admin/index.php"
    And the admin menu should appear as
      | Dashboard  |
      | Posts      |
      | Media      |
      | Pages      |
      | Comments   |
      | Appearance |
      | Plugins    |
      | Users      |
      | Tools      |
      | Settings   |

