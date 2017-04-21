# Change Log
This project is currently at an alpha stage. It will continue on the `0.*.*` branch until the first stable release.  

## [0.4.0] - 2016-10-21

### Breaking changes:

- Moved `StephenHarris\WordPressBehatExtension\Element\WPTableElement` to `StephenHarris\WordPressBehatExtension\Element\WPTable\TableElement`
- Refactored `StephenHarris\WordPressBehatExtension\Element\WPTable\TableElement` to add row and cell element decorations
- Extracted `WordPressPostContext` from `WordPressContext` and extracted helper methods into `WordPressPostRawContext` trait
- Extracted `WordPressTermContext` from `WordPressContext` and extracted helper methods into `WordPressTermTrait` trait
- Extracted `WordPressUserContext` from `WordPressContext` and extracted helper methods into `WordPressUserTrait` trait
- Extracted `Plugins\WordPressPluginContext` from `WordPressContext`
- Extracted `Options\WordPressOptionContext` from `WordPressContext`
- Extracted `WordPressUserContext` from `WordPressContext` and extracted helper methods into `WordPressUserTrait` trait
- Extracted `WordPressEditPostContext` from `WordPressAdminContext`
- Extracted a `AdminPage` page object from `WordPressAdminContext` (see http://behat-page-object-extension.readthedocs.io/en/latest/index.html)
- Replaced `When I hover over the row containing :value in the :column_text column of :table_selector` to `When I hover over the row containing :value in the :column_text` (`:table_selector` removed, and is internally set to `.wp-list-table`
- The `Given there are posts` step expects the `post_author` column to the be username of the user, not the ID.
- Split out log-in related steps into their own context and introduce  `Login` and `Dashboard` page object
- Split out the `Util\Spin` helper class from `WordPressContext` so that it can be used in other contexts (currently it is only relevant to the new `WordPressLoginContext`
- Renamed step `When I go to the edit screen for :title` to `Given I am on the edit screen fir :title`
- Renamed step `When I go to the edit :post_type screen for :title` to `Given I am on the edit :post_type screen for :title`

### Enhancements:

- Added tests to run on Travis
- Added mu-plugin to prevent dummy content (except 'Uncategorised' category) from being created when installing WordPress.
- Improved feedback for log-in errors (i.e. explicitly checks username and password).
- `WordPressAdminContext::iGoToMenuItem()` throws an exception if then admin menu could not be found 
- Added `When I am on the log-in page` and `Then I should be on the log-in page` step definitions
- Made error message for failed table comparison more explicit: show first cell values which do match
- Added `Then the admin menu should appear as` step which compares the admin menu (top-level) against a given list of strings / regular expressions.
- Added `When I click on the :link link in the header` step definition
- Added `Then I should see that post :post_title has :value in the :column_heading column` step
- Added `When I perform the bulk action :action` step
- Added `When I hover over the row for the :postTitle post` step
- Added `StephenHarris\WordPressBehatExtension\Context\Page\Element\AdminMenu` page object element
- Added step `Then I should see the error message :text`
- Added step `Then I should see the warning message :text`
- Added step `Then I should see the info message :text`
- Added step `Then I should see the success message :text`

### Bugfixes:

- Bugfix: Fixed decorations (methods require explicit overriding)
- Fixed bugs with refreshing and clearing inbox
- Introduced a temporary fix for the admin bar issue (altering its z-index so that it doesn't interfer with interacting with elements 'underneath' it). See [#1](https://github.com/stephenharris/WordPressBehatExtension/issues/1)
- Bugfix: Fixed timestamp not parsed properly from the mail file name
- Fix step implementation for clicking first-level menu item


## [0.3.0] - 2016-06-08
### Changed
- Fixed various bugs with the MailContext
- Changed email step from '...email to <email> should match "<message>"' to '...email to <email> should contain "<message>"'
- `InboxFactory` now refreshes the inbox each time its requested
- Rename extension to **WordPressBehatExtension** and updated namespace accordingly
- Correct CHANGELOG link

## [0.2.0] - 2016-06-07
### Changed
- `.gitignore` to hide IDE files
- Rename namespace **Johnbillion\ WordPressExtension** as **StephenHarris\WordPressBehat**
- Remove autoload of PHPUnit functions, use `PHPUnit_Framework_Assert` in contexts instead
- Applied PSR-2 coding standard
- Update Readme with history & aims

### Added
- This changelog
- Add Email, Inbox and InboxFactory classes to improve handling of checking e-mails sent by `wp_mail()`. 
- Adds unit test

## 0.1.0 - 2016-06-03
### Added
- Additional contexts 


[0.3.0]: https://github.com/stephenharris/WordPressBehatExtension/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/stephenharris/WordPressBehatExtension/compare/0.1.0...0.2.0

