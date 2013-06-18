<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;

use RuntimeException;

/**
 * The implementation of the query Return
 * http://wiki.payneteasy.com/index.php/PnE:Return_Transactions
 */
class ReturnQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(OrderInterface $order)
    {
        $this->validateOrder($order);

        $query = array_merge
        (
            $this->createControlCode($order),
            $this->commonQueryOptions(),
            $order->getContextData()
        );

        /**
         * @todo Amount MUST be setted in the order
         */
        if($order->getAmount())
        {
            $query['amount']    = $order->getAmount();
            $query['currency']  = $order->getCurrency();
        }

        $query['comment']   = $order->getCancelReason();

        return $this->wrapToRequest($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        $order->validateShort();

        if (strlen($order->getCancelReason()) == 0)
        {
            throw new RuntimeException('Cancel reason must be defined');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        // Checksum used to ensure that it is Merchant (and not a fraudster)
        // that initiates the return request.
        // This is SHA-1 checksum of the concatenation login + client_orderid + orderid + merchant-control
        // if amount is not specified,
        // and login + client_orderid + orderid + amount_in_cents +
        // currency + merchant-control if amount is specified
        $sign = array
        (
            $this->config['login'],
            $order->getOrderCode(),
            $order->getPaynetOrderId()
        );

        /**
         * @todo Amount MUST be setted in the order
         */
        if($order->getAmount())
        {
            $sign[] = $order->getAmountInCents();
            $sign[] = $order->getCurrency();
        }

        $sign[] = $this->config['control'];

        return array('control' => sha1(implode('', $sign)));
    }
}