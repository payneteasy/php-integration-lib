<?php
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\Utils\Validator;

/**
 * The implementation of the query Return
 * http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    static protected $requestFieldsDefinition = array
    (
        // mandatory
        array('client_orderid',     'clientOrderId',                    true,   Validator::ID),
        array('orderid',            'paynetOrderId',                    true,   Validator::ID),
        array('amount',             'amount',                           true,   Validator::AMOUNT),
        array('currency',           'currency',                         true,   Validator::CURRENCY),
        array('comment',            'comment',                          true,   Validator::MEDIUM_STRING),
        // generated
        array('control',             null,                              true,    null),
        // from config
        array('login',               null,                              true,    null)
    );

    /**
     * {@inheritdoc}
     */
    static protected $controlCodeDefinition = array
    (
        'login',
        'clientOrderId',
        'paynetOrderId',
        'amountInCents',
        'currency',
        'control'
    );
}