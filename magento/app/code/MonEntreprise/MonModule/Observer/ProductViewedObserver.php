<?php
namespace MonEntreprise\MonModule\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

class ProductViewedObserver implements ObserverInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(Observer $observer)
    {
        $productId = $observer->getEvent()->getProductId();
        $this->logger->info('Produit consulté avec ID : ' . $productId);
    }
}
