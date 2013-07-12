<?php

namespace PaynetEasy\PaynetEasyApi\Workflow;

use PaynetEasy\PaynetEasyApi\OrderData\OrderInterface;
use PaynetEasy\PaynetEasyApi\Transport\Response;

class FakeWorkflow extends AbstractWorkflow
{
    static public $response;

    public $initialApiMethod;

    public function __construct()
    {
    }

    public function processOrder(OrderInterface $order, array $callbackData = array())
    {
        return static::$response;
    }

    public function setNeededAction(Response $response)
    {
        return parent::setNeededAction($response);
    }

    public function setInitialApiMethod($class)
    {
        parent::setInitialApiMethod($class);
    }
}