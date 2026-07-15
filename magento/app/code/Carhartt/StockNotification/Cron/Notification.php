<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Cron;

use Carhartt\StockNotification\Helper\Config;
use Carhartt\StockNotification\Helper\Data;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class Notification
 * @package Carhartt\StockNotification\Cron
 */
class Notification
{
    /**
     * Notification constructor.
     * @param Data $dataHelper
     * @param Config $configHelper
     */
    public function __construct(
       protected Data $dataHelper,
       protected Config $configHelper
    ) {}

    /**
     * @return $this
     * @throws AlreadyExistsException
     */
    public function execute()
    {
        $this->dataHelper->sendEmailToCustomer(
            null,
        );

        return $this;
    }
}
