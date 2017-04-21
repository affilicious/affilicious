Feature: Posts admin page

  Background:
    Given I have a vanilla wordpress installation
      | name          | email             | username | password |
      | BDD WordPress | admin@example.com | admin    | password |
    And there are posts
      | post_title      | post_content              | post_status | post_author | post_date           |
      | Just my article | The content of my article | publish     | admin       | 2016-10-11 08:30:00 |
      | My draft        | This is just a draft      | draft       | admin       | 2016-09-02 17:00:00 |
    And I am logged in as "admin" with password "password"


  Scenario: I can view the post list
    Given I go to menu item Posts
    Then I should be on the "Posts" page
    And the post list table looks like
      | Title           | Author | Categories    | Tags |     | Date                    |
      | Just my article | admin  | Uncategorized | —    | — 0 | Published2016/10/11     |
      | My draft        | admin  | Uncategorized | —    | — 0 | Last Modified2016/09/02 |

  Scenario: I bulk trash my posts, then undo the action
    Given I go to menu item Posts
    When I select the post "Just my article" in the table
    And I select the post "My draft" in the table
    And I perform the bulk action "Move to Trash"
    Then I should see "2 posts moved to the Trash."
    And I should not see "My draft"
    And I should not see "Just my article"
    But I should see "Undo"

    When I follow "Undo"
    Then I should see "2 posts restored from the Trash."
    And the post list table looks like
      | Title           | Author | Categories    | Tags |     | Date                    |
      | Just my article | admin  | Uncategorized | —    | — 0 | Published2016/10/11     |
      | My draft        | admin  | Uncategorized | —    | — 0 | Last Modified2016/09/02 |

  @javascript
  Scenario: I trash an individual post, then undo the action
    Given I go to menu item Posts
    When I hover over the row for the "Just my article" post
    Then I should see the following row actions
      | actions    |
      | Edit       |
      | Quick Edit |
      | Trash      |
      | View       |

    When I follow "Trash"
    Then I should see "1 post moved to the Trash."
    And I should not see "Just my article"
    But I should see "Undo"

    When I follow "Undo"
    Then I should see "1 post restored from the Trash."
    And the post list table looks like
      | Title           | Author | Categories    | Tags |   | Date                    |
      | Just my article | admin  | Uncategorized | —    | — | Published 2016/10/11     |
      | My draft        | admin  | Uncategorized | —    | — | Last Modified 2016/09/02 |

  @javascript @travis-flaky
  Scenario: Quick edit posts
    Given I go to menu item Posts
    And I quick edit the post "Just my article"
    Then I should see "QUICK EDIT"

    When I fill in "post_title" with "Just another article"
    And I press "Update"
    And I wait for AJAX to finish
    Then the post list table looks like
      | Title                | Author | Categories    | Tags |   | Date                    |
      | Just another article | admin  | Uncategorized | —    | — | Published 2016/10/11     |
      | My draft             | admin  | Uncategorized | —    | — | Last Modified 2016/09/02 |


  Scenario: Viewing the edit post page
    Given I go to menu item Posts
    And I follow "Just my article"
    Then I should be on the edit "post" screen for "Just my article"