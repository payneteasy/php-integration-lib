<?php

namespace PaynetEasy\PaynetEasyApi\Query;

use PaynetEasy\PaynetEasyApi\Utils\Validator;

/**
 * @see http://wiki.payneteasy.com/index.php/PnE:Recurrent_Transactions#Process_Recurrent_Payment
 */
class MakeRebillQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',         'payment.clientPaymentId',                      true,    Validator::ID),
        array('order_desc',             'payment.description',                          true,    Validator::LONG_STRING),
        array('amount',                 'payment.amount',                               true,    Validator::AMOUNT),
        array('currency',               'payment.currency',                             true,    Validator::CURRENCY),
        array('ipaddress',              'payment.customer.ipAddress',                   true,    Validator::IP),
        array('cardrefid',              'payment.recurrentCardFrom.cardReferenceId',    true,    Validator::ID),
        array('login',                  'queryConfig.login',                            true,    Validator::MEDIUM_STRING),
        // optional
        array('comment',                'payment.comment',                              false,   Validator::MEDIUM_STRING),
        array('cvv2',                   'payment.recurrentCardFrom.cvv2',               false,   Validator::CVV2),
        array('server_callback_url',    'queryConfig.callbackUrl',                      false,   Validator::URL)
    );

    /**
     * {@inheritdoc}
     */
    static protected $signatureDefinition = array
    (
        'queryConfig.endPoint',
        'payment.clientPaymentId',
        'payment.amountInCents',
        'payment.recurrentCardFrom.cardReferenceId',
        'queryConfig.signingKey'
    );

    /**
     * {@inheritdoc}
     */
    static protected $responseFieldsDefinition = array
    (
        'type',
        'status',
        'paynet-order-id',
        'merchant-order-id',
        'serial-number'
    );

    /**
     * {@inheritdoc}
     */
    static protected $successResponseType = 'async-response';
}