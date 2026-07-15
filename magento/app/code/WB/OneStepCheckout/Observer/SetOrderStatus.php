<?php

namespace WB\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetOrderStatus implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getPayment()->getMethodInstance()->getCode() === 'checkmo') {
            $order->setStatus('pending');
        }
    }
}
