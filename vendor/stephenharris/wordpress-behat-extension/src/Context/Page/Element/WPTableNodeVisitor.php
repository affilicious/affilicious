<?php
namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

use Behat\Gherkin\Node\TableNode;

class WPTableNodeVisitor extends WPTableVisitor
{
    /**
     * @var int Row index (starting from 0, the heading row) of the row being visited
     */
    private $visitingRowIndex = 0;

    /**
     * @var int Column index (starting from 0) of the column being visited
     */
    private $visitingColumnIndex = 0;

    /**
     * @var int The index of the checkbox column (typically 0), if known, to exclude it from the TableNode hash
     */
    private $checkboxColumnIndex = null;

    /**
     * @var array a TableNode hash to pass to TableNode::__construct()
     */
    private $tableNodeHash = array();


    /**
     * Visit the table
     * @param WPTableElement $table
     * @return bool Return true to visit the table's rows. Or false not to not visit them.
     */
    public function visitTable(WPTable $table)
    {
        //Reset everything
        $this->tableNodeHash = array();
        $this->visitingRowIndex = $this->visitingColumnIndex = 0;
        $this->checkboxColumnIndex = null;
        return true;
    }

    /**
     * Called before the row's cells are visited.
     * @param TableRowElement $row The row to visit
     * @return bool Return false to not visit the row's cells. Return true to visit the cells.
     */
    public function visitRow(WPTableRow $row)
    {
        $this->tableNodeHash[$this->visitingRowIndex] = array();
        $this->visitingColumnIndex = 0;
        return true;
    }

    /**
     * Called once all the row's cells have been visited (or the row was skipped)
     *
     * @param TableRowElement $row The row element that we're finished with
     * @return bool Return false to stop parsing any more rows. True to continue;
     */
    public function leaveRow(WPTableRow $row)
    {
        $this->visitingRowIndex++;
        return true;
    }

    /**
     * Visit the cell.
     *
     * @param TableRowElement $row The row element that we're finished with
     * @return bool Return false to stop parsing any more cells in the current row. True to continue;
     */
    public function visitCell(WPTableCell $cell)
    {
        if (0 === $this->visitingRowIndex && $cell->hasClass('column-cb')) {
            $this->checkboxColumnIndex = $this->visitingColumnIndex;
        }
        //Skip column
        if (! $this->isCheckBoxColumn($this->visitingColumnIndex)) {
            $this->tableNodeHash[$this->visitingRowIndex][] = $cell->getCleanedText();
        }

        $this->visitingColumnIndex++;
        return true;
    }

    private function isCheckBoxColumn($index)
    {
        return $this->checkboxColumnIndex === $index;
    }

    public function getTableNode()
    {
        try {
            $table_node = new TableNode($this->tableNodeHash);
        } catch (\Exception $e) {
            throw new \Exception("Unable to parse post list table. Found: " . print_r($this->tableNodeHash, true));
        }
        return $table_node;
    }
}
