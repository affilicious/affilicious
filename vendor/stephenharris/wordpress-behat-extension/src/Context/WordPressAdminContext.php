<?php

namespace StephenHarris\WordPressBehatExtension\Context;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\TableNode;

use \StephenHarris\WordPressBehatExtension\Context\Page\AdminPage;

class WordPressAdminContext extends RawMinkContext implements Context
{

    public function __construct(AdminPage $adminPage)
    {
        $this->adminPage = $adminPage;
    }

    /**
     * @When I click on the :link link in the header
     */
    public function iClickOnHeaderLink($link)
    {
        $this->adminPage->clickLinkInHeader($link);
    }
    
    /**
     * @Then I should be on the :admin_page page
     */
    public function iShouldBeOnThePage($admin_page)
    {
        $this->adminPage->assertHasHeader($admin_page);
    }

    /**
     * @Given I go to menu item :item
     */
    public function iGoToMenuItem($item)
    {
        $adminMenu = $this->adminPage->getMenu();
        $adminMenu->clickMenuItem($item);
    }

    /**
     * @Then I should see the success message :text
     */
    public function iShouldSeeSuccessMessageWithText($text)
    {
        $this->adminPage->assertContainsSuccessNotice($text);
    }

    /**
     * @Then I should see the error message :text
     */
    public function iShouldSeeErrorMessageWithText($text)
    {
        $this->adminPage->assertContainsErrorNotice($text);
    }

    /**
     * @Then I should see the warning message :text
     */
    public function iShouldSeeWarningMessageWithText($text)
    {
        $this->adminPage->assertContainsWarningNotice($text);
    }

    /**
     * @Then I should see the info message :text
     */
    public function iShouldSeeInfoMessageWithText($text)
    {
        $this->adminPage->assertContainsInfoNotice($text);
    }


    /**
     * @Then the admin menu should appear as
     */
    public function theAdminMenuShouldAppearAs(TableNode $table)
    {

        $adminMenu = $this->adminPage->getMenu();
        $topLevel = $adminMenu->getTopLevelMenuItems();

        $actualHash = array();
        foreach ($topLevel as $actualMenuName) {
            $actualHash[] = array( $actualMenuName );
        }
        $actualTableNode = new TableNode($actualHash);

        if (count($topLevel) != count($table->getRows())) {
            throw new \Exception("Number of rows do not match. Found: \n" . $actualTableNode);
        }

        $expected = $table->getColumn(0);

        foreach ($topLevel as $index => $actualMenuName) {
            $expectedMenuName = $expected[ $index ];

            if (! preg_match("/$expectedMenuName/", $actualMenuName)) {
                throw new \Exception(sprintf(
                    'Expected "%s" but found "%s":' . "\n" . $actualTableNode,
                    $expectedMenuName,
                    $actualMenuName
                ));
            }
        }
    }
}
