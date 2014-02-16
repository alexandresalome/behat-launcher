<?php

use Behat\Behat\Context\BehatContext;

class FeatureContext extends BehatContext
{
    private $success;

    public function __construct(array $parameters)
    {
        $this->success = isset($parameters['success']) ? $parameters['success'] : false;
    }

    /**
     * @Given /^stuff happens$/
     */
    public function stuffHappens()
    {
        if (!$this->success) {
            throw new \RuntimeException('Success is disabled.');
        }
    }
}
