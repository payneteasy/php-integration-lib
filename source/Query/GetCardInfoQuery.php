<?PHP
namespace PaynetEasy\Paynet\Query;

use PaynetEasy\Paynet\OrderData\OrderInterface;
use PaynetEasy\Paynet\Transport\Response;
use PaynetEasy\Paynet\Exception\ValidationException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php?title=PnE%3ARecurrent_Transactions&setlang=en#Recurrent_Payments
 */
class GetCardInfoQuery extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function orderToRequest(OrderInterface $order)
    {
        return array_merge
        (
            $order->getRecurrentCardFrom()->getData(),
            $this->commonQueryOptions($order)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function updateOrder(OrderInterface $order, Response $response)
    {
        parent::updateOrder($order, $response);

        $order->getRecurrentCardFrom()
            ->setCardPrintedName($response['card-printed-name'])
            ->setExpireYear($response['expire-year'])
            ->setExpireMonth($response['expire-month'])
            ->setBin($response['bin'])
            ->setLastFourDigits($response['last-four-digits']);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateOrder(OrderInterface $order)
    {
        if(!$order->hasRecurrentCardFrom())
        {
            throw new ValidationException('Recurrent card must be defined in Order');
        }

        if (!$order->getRecurrentCardFrom()->getCardReferenceId())
        {
            throw new ValidationException('Recurrent card reference ID is not defined');
        }
    }

    protected function validateResponse(OrderInterface $order, Response $response)
    {
        parent::validateResponse($order, $response);

        if(!$order->hasRecurrentCardFrom())
        {
            throw new ValidationException('Recurrent card must be defined in Order');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createControlCode(OrderInterface $order)
    {
        // This is SHA-1 checksum of the concatenation
        // login + cardrefid + merchant-control.
        return sha1
        (
            $this->config['login'].
            $order->getRecurrentCardFrom()->getCardReferenceId().
            $this->config['control']
        );
    }
}