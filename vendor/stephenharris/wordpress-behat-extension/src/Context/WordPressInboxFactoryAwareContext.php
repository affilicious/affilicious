<?php

namespace StephenHarris\WordPressBehatExtension\Context;

/**
 * Interfaces for contexts which want to receive an inbox factory
 * This allows the context to ask for inbox associated with an e-mail address to make assertions about
 * its contents.
 */
interface WordPressInboxFactoryAwareContext
{
    public function setInboxFactory(\StephenHarris\WordPressBehatExtension\WordPress\InboxFactory $factory);
}
