Feature: Editing a post

  Background:
    Given I have a vanilla wordpress installation
      | name          | email             | username | password |
      | BDD WordPress | admin@example.com | admin    | password |
    And there are posts
      | post_title      | post_content              | post_status | post_author | post_date           |
      | Just my article | The content of my article | publish     | admin       | 2016-10-11 08:30:00 |
      | My draft        | This is just a draft      | draft       | admin       | 2016-09-02 17:00:00 |
    And I am logged in as "admin" with password "password"

  Scenario: Editing a published post
    Given I am on the edit screen for "Just my article"
    When I change the title to "Yet another article"
    And I press the update button
    Then I should see the success message "Post updated. View post"

    When I follow "View post"
    Then I should see "Yet another article"