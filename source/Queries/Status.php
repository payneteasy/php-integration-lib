<?PHP
namespace PaynetEasy\Paynet\Queries;

use \PaynetEasy\Paynet\Data\Order;

use \PaynetEasy\Paynet\Exceptions\ConfigException;

/**
 * The implementation of the query STATUS
 * http://wiki.payneteasy.com/index.php/PnE:Sale_Transactions#Order_status
 */
class Status extends Query
{
    public function validate()
    {
        $this->validateConfig();

        if(empty($this->config['login']))
        {
            throw new ConfigException('login undefined');
        }

        if(($this->order instanceof Order) === false)
        {
            throw new ConfigException('Order is not instance of Order');
        }

        $this->order->validateShort();
    }

    public function process($data = null)
    {
        $this->validate();

        $query              = array_merge
        (
            $this->order->getContextData(),
            // Выделить этот код в отдельный класс
            array
            (
                'login'         => $this->config['login'],
                'control'       => $this->createControlCode(),
                '.method'       => $this->method,
                '.end_point'    => $this->config['end_point']
            )
        );

        return $this->sendQuery($query);
    }

    protected function createControlCode()
    {
        // This is SHA-1 checksum of the concatenation
        // login + client-order-id + paynet-order-id + merchant-control.
        return sha1
        (
            $this->config['login'].
            $this->order->getOrderCode().
            $this->order->getPaynetOrderId().
            $this->config['control']
        );
    }
}