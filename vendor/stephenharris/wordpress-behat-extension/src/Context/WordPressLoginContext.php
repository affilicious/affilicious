<?php

namespace StephenHarris\WordPressBehatExtension\Context;

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\RawMinkContext;

use \StephenHarris\WordPressBehatExtension\Context\Page\Login;

/**
 * Steps relating to the log-in page.
 */
class WordPressLoginContext extends RawMinkContext implements Context, SnippetAcceptingContext
{

    public function __construct(Login $loginPage)
    {
        $this->loginPage = $loginPage;
    }

    /**
     * Login into the reserved area of this wordpress
     *
     * @Given /^I am logged in as "([^"]*)" with password "([^"]*)"$/
     */
    public function login($username, $password)
    {
        $this->getSession()->reset();
        $this->loginPage->open();
        $redirectedToPage = $this->loginPage->loginAs($username, $password);

        \PHPUnit_Framework_Assert::assertTrue(
            $redirectedToPage->isOpen(),
            'The current page should be the dashboard'
        );
    }

    /**
     * @When I am on the log-in page
     */
    public function iAmOnTheLogInPage()
    {
        $this->loginPage->open();
    }

    /**
     * @Then I should be on the log-in page
     */
    public function iShouldBeOnTheLogInPage()
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $this->loginPage->isOpen(),
            'The current page should be the log-in page'
        );
    }
}
