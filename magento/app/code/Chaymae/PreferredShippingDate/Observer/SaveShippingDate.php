<?php
namespace Chaymae\PreferredShippingDate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveShippingDate implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $date = $quote->getShippingAddress()->getData('preferred_shipping_date');

        if ($date) {
            $order->setData('preferred_shipping_date', $date);
        }
    }
}
