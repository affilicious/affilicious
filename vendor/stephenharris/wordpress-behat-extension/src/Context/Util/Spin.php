<?php

namespace StephenHarris\WordPressBehatExtension\Context\Util;

trait Spin
{

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @overide When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     * @overide When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with:$/
     * @overide When /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function fillField($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);

        $this->spin(function ($context) use ($field, $value) {
            $context->getSession()->getPage()->fillField($field, $value);
            return true;
        });
    }

    public function spin($lambda, $wait = 60)
    {
        for ($i = 0; $i < $wait; $i++) {
            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            sleep(1);
        }

        $backtrace = debug_backtrace();

        throw new Exception(
            "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n"
        );
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ")
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }
}
