<?php
namespace StephenHarris\WordPressBehatExtension\Context\Terms;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines steps related to terms
 *
 * @package StephenHarris\WordPressBehatExtension\Context
 */
class WordPressTermContext implements Context
{
    use \StephenHarris\WordPressBehatExtension\Context\Terms\WordPressTermTrait;

    /**
     * Add these terms to this WordPress installation
     *
     * Example: Given there are category terms
     *              | name  | slug | description      |
     *              | foo   | foo  | The foo category |
     *              | bar   | bar  | The bar category |
     *
     * @Given there are :taxonomy terms
     */
    public function thereAreTerms($taxonomy, TableNode $terms)
    {
        foreach ($terms->getHash() as $termData) {
            $this->insert($termData, $taxonomy);
        }
    }
}
