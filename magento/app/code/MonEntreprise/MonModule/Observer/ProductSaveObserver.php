<?php
namespace MonEntreprise\MonModule\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

class ProductSaveObserver implements ObserverInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->logger->info('Produit sauvegardé : ' . $product->getName() . ' (ID: ' . $product->getId() . ')');
    }
}
