<?php

namespace PaynetEasy\PaynetEasyApi\Callback;

use PaynetEasy\PaynetEasyApi\PaymentData\Payment;
use PaynetEasy\PaynetEasyApi\Transport\CallbackResponse;

class FakeCallback implements CallbackInterface
{
    public function processCallback(Payment $payment, CallbackResponse $callback)
    {
        return $callback;
    }
}