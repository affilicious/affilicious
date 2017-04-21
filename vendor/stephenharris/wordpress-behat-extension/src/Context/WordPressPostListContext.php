<?php

namespace StephenHarris\WordPressBehatExtension\Context;

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\RawMinkContext;

use \StephenHarris\WordPressBehatExtension\Context\Page\Element\WPTable;

/**
 * WordPress Post List context
 */
class WordPressPostListContext extends RawMinkContext implements Context, SnippetAcceptingContext
{


    public function __construct(WPTable $adminTable)
    {
        $this->adminTable = $adminTable;
    }


    /**
     * @When I hover over the row containing :value in the :column_text column
     */
    public function iHoverOverTheRowContainingInTheColumnOf($value, $column_text)
    {
        $WPTable = $this->getTable();
        $row = $WPTable->getRowWithColumnValue($value, $column_text);
        $row->mouseOver();
    }

    /**
     * @When I hover over the row for the :postTitle post
     */
    public function iHoverOverTheRowForThePost($postTitle)
    {
        $this->iHoverOverTheRowContainingInTheColumnOf($postTitle, 'Title');
    }

    /**
     *
     * Example
     *    Then I should see the following actions
     *      | actions    |
     *      | Edit       |
     *      | Quick Edit |
     *      | Trash      |
     *      | View       |
     *
     * @Then I should see the following row actions
     */
    public function iShouldSeeTheFollowingRowActions(TableNode $table)
    {

        $rows_actions = $this->getSession()->getPage()->findAll('css', '.wp-list-table tr .row-actions');

        $action_node = false;

        foreach ($rows_actions as $row_actions) {
            if ($row_actions->isVisible()) {
                $action_node = $row_actions;
                break;
            }
        }

        if (! $action_node) {
            throw new \Exception('Row actions not visible');
        }

        $action_nodes = $action_node->findAll('css', 'span');

        $hash = $table->getHash();

        foreach ($hash as $n => $expected_row) {
            if (! isset($action_nodes[$n])) {
                throw new \Exception(sprintf(
                    'Expected "%s", but there is no action at index %d.',
                    $expected_row['actions'],
                    $n
                ));
            } elseif (trim($action_nodes[$n]->getText(), ' |') != $expected_row['actions']) {
                throw new \Exception(sprintf(
                    'Expected "%s" at index %d. Instead found "%s".',
                    $expected_row['actions'],
                    $n,
                    trim($action_nodes[$n]->getText(), ' |')
                ));
            }
        }

        if (count($hash) !== count($action_nodes)) {
            throw new \Exception(sprintf(
                'Expected %d actions but found %d',
                count($hash),
                count($action_nodes)
            ));
        }
    }
    
    /**
     * @Then the post list table looks like
     */
    public function thePostListTableLooksLike(TableNode $expectedTable)
    {

        $WPTable = $this->getTable();
        $actualTable  = $WPTable->getTableNode();

        $expectedTableHeader = $expectedTable->getRow(0);
        $actualTableHeader = $actualTable->getRow(0);

        //Check table headers
        if (count($actualTableHeader) != count($expectedTableHeader)) {
            $message = "Columns do no match:\n";
            $message .= $actualTable->getTableAsString();
            throw new \Exception($message);
        } else {
            foreach ($expectedTableHeader as $index => $column) {
                if ($column != $actualTableHeader[$index]) {
                    $message = "Columns do no match:\n";
                    $message .= $actualTable->getTableAsString();
                    throw new \Exception($message);
                }
            }
        }

        //Check rows
        $expectedRows = $expectedTable->getRows();
        foreach ($expectedRows as $rowIndex => $rowColumns) {
            $actualRow = $actualTable->getRow($rowIndex);

            foreach ($rowColumns as $column => $expectedCellValue) {
                if (trim($expectedCellValue) != $actualRow[$column]) {
                    $message = sprintf(
                        "(Row %d) %s does not match expected %s:\n",
                        $rowIndex,
                        $actualRow[$column],
                        $expectedCellValue
                    );
                    $message .= $actualTable->getTableAsString();
                    throw new \Exception($message);
                }
            }
        }
    }

    /**
     * @Then I should see that post :post_title has :value in the :column_heading column
     */
    public function iShouldSeeThatBookingHasInTheColumn($post_title, $value, $column_heading)
    {

        $WPTable = $this->getTable();
        $row = $WPTable->getRowWithColumnValue($post_title, 'Title');
        $columnIndex = $WPTable->getColumnIndexWithHeading($column_heading);

        $cell = $row->getCell($columnIndex);

        $actual = $cell->getText();
        if ($actual != $value) {
            throw new Exception('Expected: %s. Found: %s', $value, $actual);
        }
    }

    /**
     * @When I select the post :arg1 in the table
     */
    public function iSelectThePostInTheTable($arg1)
    {
        $WPTable = $this->getTable();
        $row = $WPTable->getRowWithColumnValue($arg1, 'Title');
        $row->check();
    }


    /**
     * @When I quick edit the post :arg1
     */
    public function iQuickEdit($arg1)
    {
        $WPTable = $this->getTable();
        $row = $WPTable->getRowWithColumnValue($arg1, 'Title');
        
        $row->mouseOver();
        $quick_edit_link = $row->find('css', '.editinline');
        $quick_edit_link->click();
    }

    /**
     * @When I perform the bulk action :action
     */
    public function iPerformTheBulkAction($action)
    {
        $this->getSession()->getPage()->selectFieldOption('action', $action);
        $this->getSession()->getPage()->pressButton('doaction');
    }

    public function getTable()
    {
        return $this->adminTable;
    }
}
