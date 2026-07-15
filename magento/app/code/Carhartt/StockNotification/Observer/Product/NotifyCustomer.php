<?php

declare(strict_types=1);

namespace Carhartt\StockNotification\Observer\Product;

use Carhartt\StockNotification\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Carhartt\StockNotification\Model\ResourceModel\Notification\CollectionFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Phrase;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableType;


/**
 * Class NotifyCustomer
 * @package Carhartt\StockNotification\Observer\Product
 */
class NotifyCustomer implements ObserverInterface
{
    /**
     * @param CollectionFactory $notificationCollectionFactory
     * @param Data $dataHelper
     * @param ConfigurableType $configurableType
     */
    public function __construct(
        protected CollectionFactory $notificationCollectionFactory,
        protected Data $dataHelper,
        protected ConfigurableType $configurableType
    ) {}

    /**
     * @param Observer $observer
     * @return $this
     * @throws MailException
     * @throws AlreadyExistsException|LocalizedException
     */
    public function execute(Observer $observer)
    {
        $data = $observer->getData();
        $productIds = $data['products'] ?? [];

        if (!empty($productIds)) {

            $parentIds = $this->configurableType->getParentIdsByChild($productIds);
            $parentIds = array_unique($parentIds);
            if (!empty($parentIds)) {
                try {
                    $this->dataHelper->sendEmailToCustomer($parentIds);
                } catch (MailException $e) {
                    throw new MailException(new Phrase($e->getMessage()), $e);
                }
            }
        }
        return $this;
    }
}
