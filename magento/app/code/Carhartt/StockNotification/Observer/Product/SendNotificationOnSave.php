<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Observer\Product;

use Carhartt\StockNotification\Helper\Data as DataHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SendNotificationOnSave implements ObserverInterface
{
    public function __construct(
        protected DataHelper $dataHelper,
        protected LoggerInterface $notificationLogger
    ) {
    }

    public function execute(Observer $observer): void
    {
        $product = $observer->getEvent()->getProduct();
        if (!$product || !$product->getId()) {
            return;
        }

        if (!$product->isSaleable()) {
            return;
        }

        try {
            $this->dataHelper->sendEmailToCustomer([$product->getId()]);
            $this->notificationLogger->info(
                'Immediate stock notification check triggered on save for product: ' . $product->getId()
            );
        } catch (\Exception $e) {
            $this->notificationLogger->error('Immediate notification error: ' . $e->getMessage());
        }
    }
}
