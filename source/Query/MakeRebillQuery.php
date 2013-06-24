<?php

namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;

class MakeRebillQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                        true,    Validator::ID),
        array('order_desc',         'description',                          true,    Validator::ID),
        array('amount',             'amount',                               true,    Validator::AMOUNT),
        array('currency',           'currency',                             true,    Validator::CURRENCY),
        array('ipaddress',          'ipAddress',                            true,    Validator::IP),
        array('cardrefid',          'recurrentCardFrom.cardReferenceId',    true,    Validator::ID),
        // optional
        array('comment',            'comment',                              false,   Validator::MEDIUM_STRING),
        array('cvv2',               'recurrentCardFrom.cvv2',               false,   Validator::CVV2),
        // generated
        array('control',             null,                                  true,    null),
        // from config
        array('login',               null,                                  true,    null),
        array('server_callback_url', null,                                  false,   null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'end_point',
        'clientOrderId',
        'amountInCents',
        'recurrentCardFrom.cardReferenceId',
        'control'
    );
}