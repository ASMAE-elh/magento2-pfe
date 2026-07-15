<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Carhartt\StockNotification\Api;

use Carhartt\StockNotification\Api\Data\NotificationInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Notification RepositoryInterface
 * @api
 * @since 100.0.2
 */
interface NotificationRepositoryInterface
{
    public function save(NotificationInterface $notification): NotificationInterface;

    /**
     * Retrieve Notification by ID.
     *
     * @param int $id
     * @return NotificationInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): NotificationInterface;


    /**
     * Delete Notification entity.
     *
     * @param NotificationInterface $notification
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(NotificationInterface $notification): bool;

    /**
     * Delete Notification entity by ID.
     *
     * @param int $notificationId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $notificationId): bool;

    /**
     * @return NotificationInterface
     */
    public function create(): NotificationInterface;
}
