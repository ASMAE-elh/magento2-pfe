<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Api\Data;

/**
 * Notification Interface
 */
interface NotificationInterface
{
    /** @var string  */
    public const ENTITY_ID       = 'entity_id';

    /** @var string  */
    public const CUSTOMER_ID     = 'customer_id';

    /** @var string  */
    public const CUSTOMER_EMAIL  = 'customer_email';

    /** @var string  */
    public const PRODUCT_ID      = 'product_id';

    /** @var string  */
    public const PRODUCT_SKU     = 'product_sku';

    /** @var string  */
    public const STORE_ID        = 'store_id';

    /** @var string  */
    public const STATUS          = 'status';

    /** @var string  */
    public const CREATED_AT      = 'created_at';

    /** @var string  */
    const NOTIFICATION_STATUS_PENDING = 'pending';

    /** @var string  */
    const NOTIFICATION_STATUS_SUBSCRIBED = 'confirmed';

    /** @var string  */
    const NOTIFICATION_STATUS_UNSUBSCRIBED = 'canceled';

    /** @var string  */
    const NOTIFICATION_STATUS_CLOSED = 'closed';

    /**
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * @return int|null
     */
    public function getProductId(): ?int;

    /**
     * @return string
     */
    public function getProductSku(): string;

    /**
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param int $entityId
     * @return self
     */
    public function setEntityId(int $entityId): self;

    /**
     * @param int|null $customerId
     * @return self
     */
    public function setCustomerId(?int $customerId): self;

    /**
     * @param string $email
     * @return self
     */
    public function setCustomerEmail(string $email): self;

    /**
     * @param int $productId
     * @return self
     */
    public function setProductId(int $productId): self;

    /**
     * @param string $sku
     * @return self
     */
    public function setProductSku(string $sku): self;

    /**
     * @param int $storeId
     * @return self
     */
    public function setStoreId(int $storeId): self;

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self;

    /**
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt(string $createdAt): self;
}
