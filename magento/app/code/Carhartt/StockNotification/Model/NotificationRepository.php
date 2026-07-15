<?php

declare(strict_types=1);

namespace Carhartt\StockNotification\Model;

use Carhartt\StockNotification\Api\NotificationRepositoryInterface;
use Carhartt\StockNotification\Api\Data\NotificationInterface;
use Carhartt\StockNotification\Model\ResourceModel\Notification\CollectionFactory;
use Exception;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Carhartt\StockNotification\Model\ResourceModel\Notification as NotificationResourceModel;

/**
 * Notification Repository
 *
 * Provides CRUD operations and list retrieval for the Notification entity.
 * Acts as a bridge between the business logic layer and database layer,
 * ensuring all data access is handled through a consistent API.
 *
 * @category  Carhartt
 * @package   Carhartt_StockNotification
 */
class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * @param NotificationFactory $notificationFactory
     * @param NotificationResourceModel $resource
     */
    public function __construct(
        protected NotificationFactory       $notificationFactory,
        protected NotificationResourceModel $resource,
    )
    {
    }

    /**
     * @param int $id
     * @return NotificationInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): NotificationInterface
    {
        $notification = $this->notificationFactory->create();
        $this->resource->load($notification, $id);
        if (!$notification->getId()) {
            throw new NoSuchEntityException(__('Notification with ID "%1" not found.', $id));
        }
        return $notification;
    }

    /**
     * @param int $notificationId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $notificationId): bool
    {
        try {
            $notification = $this->getById($notificationId);
            $this->resource->delete($notification);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Notification.
     *
     * @param NotificationInterface $notification
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(NotificationInterface $notification): bool
    {
        try {
            $this->resource->delete($notification);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param NotificationInterface $notification
     * @return NotificationInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(NotificationInterface $notification): NotificationInterface
    {
        try {
            $this->resource->save($notification);
        } catch (LocalizedException $ex) {
            throw new LocalizedException(__($ex->getMessage()));
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $notification;
    }

    /**
     * @return NotificationInterface
     */
    public function create(): NotificationInterface
    {
        return $this->notificationFactory->create();
    }
}
