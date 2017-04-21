<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use Behat\Mink\Session;

class WPTable extends Element
{
    use \StephenHarris\WordPressBehatExtension\StripHtml;

    /**
     * @var array|string $selector
     */
    protected $selector = '.wp-list-table';

    private $rows;

    private $headingRow;

    private $session;

    /**
     * A bit of a hack, we require the Session to create instances of WPTableRow, so we store it here
     * @param NodeElement $node
     * @param Session $session
     */
    public function __construct(Session $session, Factory $factory)
    {
        parent::__construct($session, $factory);
        $this->session = $session;
    }


    public function accept(WPTableVisitor $visitor)
    {

        if ($visitor->visitTable($this)) {
            if (! $this->getHeadingRow()->accept($visitor)) {
                return;
            }

            foreach ($this->getRows() as $row) {
                $continue = $row->accept($visitor);
                if (! $continue) {
                    break;
                }
            }
        }
    }


    /**
     * Return a table node, i.e. extract the data of the table
     *@return
     */
    public function getTableNode()
    {
        $visitor = new WPTableNodeVisitor();
        $this->accept($visitor);
        return $visitor->getTableNode();
    }

    /**
     * Get the table's header cells
     */
    public function getHeadingRow()
    {
        if (is_null($this->headingRow)) {
            $headingRowNode = $this->find('css', 'thead tr');

            if (! $headingRowNode) {
                throw new \Exception('Table does not contain any columns (thead tr)');
            }

            $this->headingRow = new WPTableRow($headingRowNode, $this->session);
        }

        return $this->headingRow;
    }

    /**
     * Get the table's rows
     * @param array of WPTableRow (corresponding to each row)
     */
    private function getRows()
    {
        if (is_null($this->rows)) {
            $rows = $this->findAll('css', 'tbody tr');
            if (! $rows) {
                throw new \Exception('Table does not contain any rows (tbody tr)');
            }
            $this->rows = $this->decorateWithTableRow($rows);
        }

        return $this->rows;
    }

    private function decorateWithTableRow($rows)
    {
        $tableRows = array();
        foreach ($rows as $row) {
            $tableRows[] = new WPTableRow($row, $this->session);
        }
        return $tableRows;
    }



    /**
     * Return a row which contains the value in a specified column.
     *
     * @param string $value The value to look for
     * @param string $columnHeader The header (identified by label) of the column to look in
     * @return NodeElement The DOM element corresponding of the (first such) row
     * @throws \Exception
     */
    public function getRowWithColumnValue($value, $columnHeader)
    {

        $columnIndex = $this->getColumnIndexWithHeading($columnHeader);
        $rows = $this->getRows();

        foreach ($rows as $row) {
            $cell = $row->getCell($columnIndex);
            if (strpos(strtolower($cell->getText()), strtolower($value)) === false) {
                continue;
            }
            return $row;
        }

        throw new \Exception("Could not find row with {$value} in the {$columnHeader} column");
    }

    /**
     * Return the index (starting from 0) of the column with the provided heading.
     *
     * @param string $value The value to look for
     * @return int The index
     * @throws \Exception If a matching column could not be found
     */
    public function getColumnIndexWithHeading($columnHeading)
    {
        $headingRow     = $this->getHeadingRow();
        $columnIndex = false;

        foreach ($headingRow->getCells() as $index => $column) {
            if ($columnHeading === $column->getText()) {
                $columnIndex = $index;
                break;
            }
        }

        if (false === $columnIndex) {
            throw new \Exception("Could not find column '{$columnHeading}'");
        }

        return $columnIndex;
    }
}
