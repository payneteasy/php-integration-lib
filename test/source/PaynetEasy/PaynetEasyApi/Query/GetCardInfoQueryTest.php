<?php


namespace PaynetEasy\PaynetEasyApi\Query;
use PaynetEasy\PaynetEasyApi\Query\Prototype\SyncQueryTest;
use PaynetEasy\PaynetEasyApi\Transport\Response;
use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\PaymentData\RecurrentCard;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-07-29 at 16:38:15.
 */
class GetCardInfoQueryTest extends SyncQueryTest
{
    /**
     * @var GetCardInfoQuery
     */
    protected $object;

    protected $successType = 'get-card-info-response';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new GetCardInfoQuery('get-card-info');
    }

    public function testCreateRequestProvider()
    {
        return array(array
        (
            sha1
            (
                self::LOGIN .
                self::RECURRENT_CARD_FROM_ID .
                self::SIGNING_KEY
            )
        ));
    }

    /**
     * @dataProvider testProcessResponseApprovedProvider
     */
    public function testProcessResponseApproved(array $response)
    {
        $paymentTransaction = $this->getPaymentTransaction();
        $recurrentCard      = $paymentTransaction->getPayment()->getRecurrentCardFrom();
        $responseObject     = new Response($response);

        $this->object->processResponse($paymentTransaction, $responseObject);

        $this->assertEquals($responseObject['card-printed-name'], $recurrentCard->getCardPrintedName());
        $this->assertEquals($responseObject['expire-year'],       $recurrentCard->getExpireYear());
        $this->assertEquals($responseObject['expire-month'],      $recurrentCard->getExpireMonth());
        $this->assertEquals($responseObject['bin'],               $recurrentCard->getBin());
        $this->assertEquals($responseObject['last-four-digits'],  $recurrentCard->getLastFourDigits());

        return array($paymentTransaction, $responseObject);
    }

    public function testProcessResponseApprovedProvider()
    {
        return array(array(array
        (
            'type'              =>  $this->successType,
            'paynet-order-id'   =>  self::PAYNET_ID,
            'merchant-order-id' =>  self::CLIENT_ID,
            'serial-number'     =>  md5(time()),
            'card-printed-name' => 'Vasya Pupkin',
            'expire-month'      => '12',
            'expire-year'       => '14',
            'bin'               => '4485',
            'last-four-digits'  => '9130'
        )));
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Response clientId 'invalid' does not match Payment clientId
     */
    public function testProcessErrorResponseWithInvalidId()
    {
        $response = new Response(array
        (
            'type'              => 'error',
            'client_orderid'    => 'invalid',
            'card-printed-name' => 'Vasya Pupkin',
            'expire-month'      => '12',
            'expire-year'       => '14',
            'bin'               => '4485',
            'last-four-digits'  => '9130'
        ));

        $this->object->processResponse($this->getPaymentTransaction(), $response);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Response clientId '_' does not match Payment clientId
     */
    public function testProcessSuccessResponseWithInvalidId()
    {
        $response = new Response(array
        (
            'type'              => $this->successType,
            'paynet-order-id'   => '_',
            'merchant-order-id' => '_',
            'serial-number'     => '_',
            'card-ref-id'       => '_',
            'redirect-url'      => '_',
            'client_orderid'    => 'invalid',
            'card-printed-name' => 'Vasya Pupkin',
            'expire-month'      => '12',
            'expire-year'       => '14',
            'bin'               => '4485',
            'last-four-digits'  => '9130'
        ));

        $this->object->processResponse($this->getPaymentTransaction(), $response);
    }

    protected function getPayment()
    {
        return new Payment(array
        (
            'client_id'             => self::CLIENT_ID,
            'paynet_id'             => self::PAYNET_ID,
            'recurrent_card_from'   => new RecurrentCard(array
            (
                'paynet_id'     => self::RECURRENT_CARD_FROM_ID
            ))
        ));
    }
}