<?php
namespace StephenHarris\WordPressBehatExtension\Context\Users;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines steps related to terms
 *
 * @package StephenHarris\WordPressBehatExtension\Context
 */
class WordPressUserContext implements Context
{
    use \StephenHarris\WordPressBehatExtension\Context\Users\WordPressUserTrait;

    /**
     * Add these users to this wordpress installation
     *
     * @see wp_insert_user
     *
     * @Given /^there are users$/
     */
    public function thereAreUsers(TableNode $table)
    {
        foreach ($table->getHash() as $userData) {
            $this->insert($userData);
        }
    }
}
