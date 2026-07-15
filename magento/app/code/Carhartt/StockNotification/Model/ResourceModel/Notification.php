<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Model\ResourceModel;

use Carhartt\StockNotification\Api\Data\NotificationInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource Model for Stock Notification table
 */
class Notification extends AbstractDb
{
    /** @var string Table Name */
    public const  TABLE_NAME = 'carhartt_stock_notification';

    /**
     * Define main table and primary key
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, NotificationInterface::ENTITY_ID);
    }
}
