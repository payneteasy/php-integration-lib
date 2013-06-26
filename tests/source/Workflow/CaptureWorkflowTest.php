<?php

namespace PaynetEasy\Paynet\Workflow;

use PaynetEasy\Paynet\OrderData\Order;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Transport\FakeGatewayClient;
use PaynetEasy\Paynet\Query\QueryFactory;
use PaynetEasy\Paynet\Callback\CallbackFactory;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-26 at 12:30:43.
 */
class CaptureWorkflowTest extends \PHPUnit_Framework_TestCase
{
    const LOGIN             = 'test-login';
    const END_POINT         =  789;
    const SIGN_KEY          = 'D5F82EC1-8575-4482-AD89-97X6X0X20X22';
    const CLIENT_ORDER_ID   = 'CLIENT-112233';
    const PAYNET_ORDER_ID   = 'PAYNET-112233';

    /**
     * @var CaptureWorkflow
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CaptureWorkflow(new FakeGatewayClient('_'),
                                            new QueryFactory,
                                            new CallbackFactory,
                                            $this->getConfig());
    }

    /**
     * @dataProvider testProcessOrderProvider
     */
    public function testProcessOrder($responseData, $stageBefore, $stageAfter, $expectedMethod = null)
    {
        FakeGatewayClient::$response = new Response($responseData);

        $order = $this->getOrder();

        if ($stageBefore)
        {
            $order->setTransportStage($stageBefore);
        }

        $this->object->processOrder($order, $responseData);

        $this->assertEquals($stageAfter, $order->getTransportStage());

        if ($expectedMethod)
        {
            $this->assertEquals($expectedMethod, FakeGatewayClient::$request->getApiMethod());
        }
    }

    public function testProcessOrderProvider()
    {
        return array(
        array
        (
            array
            (
                'type'              => 'async-response',
                'status'            => 'processing',
                'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
                'merchant-order-id' =>  self::CLIENT_ORDER_ID,
                'serial-number'     =>  md5(time())
            ),
            null,
            Order::STAGE_CREATED,
            'capture'
        ),
        array
        (
            array
            (
                'type'              => 'status-response',
                'status'            => 'processing',
                'paynet-order-id'   =>  self::PAYNET_ORDER_ID,
                'merchant-order-id' =>  self::CLIENT_ORDER_ID,
                'serial-number'     =>  md5(time())
            ),
            Order::STAGE_CREATED,
            Order::STAGE_CREATED,
            'status'
        ),
        array
        (
            array
            (
                'status'            => 'approved',
                'amount'            =>  99.1,
                'orderid'           =>  self::PAYNET_ORDER_ID,
                'merchant_order'    =>  self::CLIENT_ORDER_ID,
                'client_orderid'    =>  self::CLIENT_ORDER_ID,
                'control'           => sha1
                (
                    'approved' .
                    self::PAYNET_ORDER_ID .
                    self::CLIENT_ORDER_ID .
                    self::SIGN_KEY
                )
            ),
            Order::STAGE_REDIRECTED,
            Order::STAGE_ENDED,
            null
        ));
    }

    protected function getOrder()
    {
        return new Order(array
        (
            'client_orderid'        => self::CLIENT_ORDER_ID,
            'paynet_order_id'       => self::PAYNET_ORDER_ID,
            'amount'                => 99.1,
            'currency'              => 'EUR'
        ));
    }

    /**
     * @return      array
     */
    protected function getConfig()
    {
        return array
        (
            'login'                 =>  self::LOGIN,
            'end_point'             =>  self::END_POINT,
            'control'               =>  self::SIGN_KEY,
            'redirect_url'          => 'https://example.com/redirect_url',
            'server_callback_url'   => 'https://example.com/callback_url'
        );
    }
}
