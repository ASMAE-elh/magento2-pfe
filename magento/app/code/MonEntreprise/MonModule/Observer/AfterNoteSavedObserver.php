<?php
namespace MonEntreprise\MonModule\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

class AfterNoteSavedObserver implements ObserverInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(Observer $observer)
    {
        $note = $observer->getEvent()->getNote();

        $this->logger->info('=== Note sauvegardée ===');
        $this->logger->info('Titre   : ' . $note->getTitle());
        $this->logger->info('Contenu : ' . $note->getContent());
        $this->logger->info('ID      : ' . $note->getNoteId());
    }
}
