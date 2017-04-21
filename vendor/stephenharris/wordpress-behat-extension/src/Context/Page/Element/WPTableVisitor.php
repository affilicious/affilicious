<?php
namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

/**
 * The TableRowElement 'decorates' NodeElement. It adds context the <tr> element such as getting
 * values from a specific column.
 *
 * Please note that this Decorator implementation is lazy and cannot be stacked. See link below for details.
 *
 * @link http://jrgns.net/decorator-pattern-implemented-properly-in-php/
 * @package StephenHarris\WordPressExtension\Element
 */
abstract class WPTableVisitor
{

    /**
     * Visit the table
     * @param TableElement $table
     * @return bool Return true to visit the table's rows. Or false not to not visit them.
     */
    public function visitTable(WPTable $table)
    {
        return true;
    }

    /**
     * Called before the row's cells are visited.
     * @param TableRowElement $row The row to visit
     * @return bool Return false to not visit the row's cells. Return true to visit the cells.
     */
    public function visitRow(WPTableRow $row)
    {
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
        return true;
    }
}
