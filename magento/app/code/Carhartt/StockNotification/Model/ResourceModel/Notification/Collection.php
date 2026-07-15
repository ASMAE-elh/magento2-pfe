<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Model\ResourceModel\Notification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Carhartt\StockNotification\Model\Notification;
use Carhartt\StockNotification\Model\ResourceModel\Notification as NotificationResource;

/**
 * Collection class for Stock Notification
 */
class Collection extends AbstractCollection
{
    /**
     * Define model and resource model
     */
    protected function _construct(): void
    {
        $this->_init(Notification::class, NotificationResource::class);
    }
}
