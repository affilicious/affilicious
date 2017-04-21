<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

class WPTableCell extends NodeElement
{
    use \StephenHarris\WordPressBehatExtension\StripHtml;

    public function __construct(NodeElement $node, Session $session)
    {
        parent::__construct($node->getXpath(), $session);
    }

    public function accept(WPTableVisitor $visitor)
    {
        return $visitor->visitCell($this);
    }

    /**
     * Returns an array of visible text in the row.
     * @return array
     */
    public function getCleanedText()
    {
        if ($this->find('css', '.row-title')) {
            //The title column will contain action links, we just want the title text
            return trim($this->find('css', '.row-title')->getText());
        } elseif ($this->find('css', '.screen-reader-text')) {
            //Exclude screen reader text
            return trim($this->extractNonScreenReaderText($this));
        } else {
            return trim($this->getText());
        }
    }

    private function extractNonScreenReaderText($node)
    {

        if ($node->hasClass('screen-reader-text')) {
            return '';
        }

        $children = $node->findAll('xpath', '/*');
        $text = array();
        if ($children) {
            foreach ($children as $child) {
                $text[] = $this->extractNonScreenReaderText($child);
            }
        } else {
            $text[] = $node->getText();
        }

        $text = array_filter($text, function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        return implode(' ', $text);
    }

    public function isInCheckboxColumn()
    {
        return $this->columnHeaderNodeElement->hasClass('column-cb');
    }
}
