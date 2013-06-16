<?php

namespace PaynetEasy\Paynet;

use PaynetEasy\Paynet\Data\OrderInterface;

use PaynetEasy\Paynet\Data\Order;
use PaynetEasy\Paynet\Transport\Request;
use PaynetEasy\Paynet\Transport\Response;

use PaynetEasy\Paynet\Workflow\FakeWorkflow;
use PaynetEasy\Paynet\Queries\FakeQuery;
use PaynetEasy\Paynet\Transport\FakeGatewayClient;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-16 at 14:01:06.
 */
class OrderProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PublicOrderProcessor
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PublicOrderProcessor('_');
    }

    /**
     * @dataProvider testExecuteWorkflowProvider
     */
    public function testExecuteWorkflow($neededAction, $eventName)
    {
        $response = new Response;
        $response->setNeededAction($neededAction);

        FakeWorkflow::$response = $response;

        $listenerCalled = false;
        $eventListener  = function() use (&$listenerCalled)
        {
            $listenerCalled = true;
        };

        $this->object->setEventListener($eventName, $eventListener);
        $this->object->executeWorkflow('fake', array(), new Order(array()));

        $this->assertTrue($listenerCalled);
    }

    public function testExecuteWorkflowProvider()
    {
        return(array(
        array
        (
            Response::NEEDED_REDIRECT,
            OrderProcessor::EVENT_REDIRECT_RECEIVED
        ),
        array
        (
            Response::NEEDED_SHOW_HTML,
            OrderProcessor::EVENT_HTML_RECEIVED
        ),
        array
        (
            Response::NEEDED_STATUS_UPDATE,
            OrderProcessor::EVENT_STATUS_NOT_CHANGED
        )));
    }

    public function testExecuteQuery()
    {
        FakeQuery::$request             = new Request;
        FakeGatewayClient::$response    = new Response;

        $listenerCalled = false;
        $eventListener  = function() use (&$listenerCalled)
        {
            $listenerCalled = true;
        };

        $this->object->setGatewayClient(new FakeGatewayClient);
        $this->object->setEventListener(OrderProcessor::EVENT_ORDER_CHANGED, $eventListener);

        $this->object->executeQuery('fake', array(), new Order(array()));

        $this->assertTrue($listenerCalled);
    }

    public function testEventListeners()
    {
        $this->object->setEventListeners(array
        (
            OrderProcessor::EVENT_ORDER_CHANGED => function (){},
            OrderProcessor::EVENT_HTML_RECEIVED => function (){}
        ));

        $this->assertCount(2, $this->object->eventListeners);
        $this->assertArrayHasKey(OrderProcessor::EVENT_ORDER_CHANGED, $this->object->eventListeners);
        $this->assertArrayHasKey(OrderProcessor::EVENT_HTML_RECEIVED, $this->object->eventListeners);

        $this->object->removeEventListener(OrderProcessor::EVENT_ORDER_CHANGED);

        $this->assertCount(1, $this->object->eventListeners);
        $this->assertArrayNotHasKey(OrderProcessor::EVENT_ORDER_CHANGED, $this->object->eventListeners);
        $this->assertArrayHasKey(OrderProcessor::EVENT_HTML_RECEIVED, $this->object->eventListeners);

        $this->object->removeEventListeners();

        $this->assertEmpty($this->object->eventListeners);
    }

    public function testFireEvent()
    {
        $listenerCalled = false;
        $eventListener  = function() use (&$listenerCalled)
        {
            $listenerCalled = true;
        };

        $this->object->setEventListener(OrderProcessor::EVENT_ORDER_CHANGED, $eventListener);
        $this->object->fireEvent(OrderProcessor::EVENT_ORDER_CHANGED, new Order(array()), new Response);

        $this->assertTrue($listenerCalled);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown event name: _
     */
    public function testSetEventListenerWrongName()
    {
        $this->object->setEventListener('_', 'not_callable');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Event listener must be callable
     */
    public function testSetEventListenerNotCallable()
    {
        $this->object->setEventListener(OrderProcessor::EVENT_ORDER_CHANGED, 'not_callable');
    }
}

class PublicOrderProcessor extends OrderProcessor
{
    public $eventListeners = array();

    public function fireEvent($eventName, OrderInterface $order, Response $response = null)
    {
        parent::fireEvent($eventName, $order, $response);
    }
}