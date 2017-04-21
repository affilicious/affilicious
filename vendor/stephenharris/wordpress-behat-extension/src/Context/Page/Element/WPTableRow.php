<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

class WPTableRow extends NodeElement
{
    use \StephenHarris\WordPressBehatExtension\StripHtml;

    private $cells = null;

    private $session;

    /**
     * A bit of a hack, we require the Session to create instances of WPTableRow, so we store it here
     * @param NodeElement $node
     * @param Session $session
     */
    public function __construct(NodeElement $node, Session $session)
    {
        parent::__construct($node->getXpath(), $session);
        $this->session = $session;
    }

    public function accept(WPTableVisitor $visitor)
    {
        if ($visitor->visitRow($this)) {
            foreach ($this->getCells() as $cell) {
                $continue = $cell->accept($visitor);
                if (! $continue) {
                    break;
                }
            }
        }
        return $visitor->leaveRow($this);
    }

    public function getCells()
    {
        if (is_null($this->cells)) {
            $this->rows = array();
            foreach ($this->findAll('css', 'td,th') as $i => $cellNode) { //cells can be th or td
                $this->cells[] = new WPTableCell($cellNode, $this->session);
            };
        }
        return $this->cells;
    }

    public function getCell($index)
    {
        $cells = $this->getCells();
        return $cells[ $index ];
    }

    /**
     * Checks the current row
     */
    public function check()
    {
        $this->getCheckbox()->check();
    }

    /**
     * Unchecks current node if it's a checkbox field.
     */
    public function uncheck()
    {
        $this->getCheckbox()->uncheck();
    }

    /**
     * Checks whether this row is checked
     * @return Boolean
     */
    public function isChecked()
    {
        $this->getCheckbox()->isChecked();
    }

    private function getCheckbox()
    {
        return $this->find('css', '.check-column input[type=checkbox]');
    }
}
