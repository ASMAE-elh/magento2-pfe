<?php

namespace WB\OneStepCheckout\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\OrderFactory;

class PlaceOrder extends Action
{
    protected $resultJsonFactory;
    protected $orderFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        OrderFactory $orderFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $orderId = $this->getRequest()->getParam('orderId');

        if ($orderId) {
            try {
                $order = $this->orderFactory->create()->load($orderId);
                if ($order->getId()) {
                    // Additional processing if needed

                    return $result->setData(['status' => 'success', 'redirectUrl' => $this->getRedirectUrl()]);
                }
            } catch (\Exception $e) {
                return $result->setData(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }

        return $result->setData(['status' => 'error', 'message' => 'Invalid order ID.']);
    }

    protected function getRedirectUrl()
    {
        return $this->getUrl('checkout/onepage/success');
    }
}
