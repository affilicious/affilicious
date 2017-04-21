# Helper Classes

While WordPressBehatExtension provides a number of steps relating to WordPress and its UI, it also makes it very easy to define your own steps for your plug-in or theme.

Firstly, assuming you have included the `WordPressContext` class in your `behat.yml`, all WordPress functions and classes are available in you context classes.

However, in addition WordPressBehatExtension provides a layer to sit between your context definitions and WordPress. They take the form of [traits](http://php.net/manual/en/language.oop5.traits.php) and provide helpful methods for interacting with WordPress.


## An example: custom post types

Let's suppose you are developing plug-in which sells books. An example feature might be:

    Scenario: I can view details of the book
      Given I have a vanilla wordpress installation
        | name         | email             | username | password |
        | My Book Shop | admin@example.com | admin    | password |
      And there are books
        | name                   | author     | ISBN       | price (£) |
        | To Kill A Mocking Bird | Harper Lee | 1455538965 | 4.74      |
    
      And I am on the homepage
      Then I should see "To Kill A Mocking Bird"
    
      When I follow "To Kill A Mocking Bird"
      Then I should see "Author: Harper Lee"
      And I should see "ISBN: 1455538965"
    

Clearly, part of the itended functionality for this (very simplistic) plug-in is that it lists the books on the homepage, and that clicking the link through the single book page should list the details of the book, namely the author's name and the ISBN number.

If we try to run this test we find that there is no definition for the `And there are books` step:

```
--- FeatureContext has missing steps. Define them with these snippets:

    /**
     * @Given there are books
     */
    public function thereAreBooks(TableNode $table)
    {
        throw new PendingException();
    }
```

While we can use `wp_insert_post()` to create the book in this step we can instead use the `\StephenHarris\WordPressBehatExtension\Context\PostTypes\WordPressPostTrait;`:


```php
use Behat\Behat\Context\Context,
    Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Features context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext {
    use \StephenHarris\WordPressBehatExtension\Context\PostTypes\WordPressPostTrait;

    /**
     * @Given there are books
     */
    public function thereAreBooks(TableNode $table)
    {
        foreach ($table->getHash() as $bookData) {
            $bookID = $postData = array(
                'post_title' => $bookData['title']
            );
            $this->insert($postData);
            update_post_meta($bookID, 'book_author', $bookData['author']);
            update_post_meta($bookID, 'isbn', $bookData['ISBN']);
            update_post_meta($bookID, 'price', $bookData['price (£)']);
        }
    }

}
```

What has using `WordPressPostTrait;` bought us? It automatically checks that the post has been created successfully. If it does not, it throws an exception with an informative error message which fails the scenerio. 

By using this helper class we can keep our step definition clean of any sanity-checking code, making it easier to read.
