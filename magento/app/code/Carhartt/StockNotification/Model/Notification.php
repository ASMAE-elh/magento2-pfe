<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Model;

use Carhartt\StockNotification\Helper\Config;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Carhartt\StockNotification\Api\Data\NotificationInterface;
use Carhartt\StockNotification\Model\ResourceModel\Notification as NotificationResource;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Notification Model Class
 */
class Notification extends AbstractModel implements NotificationInterface
{
    /**
     * Notification cache tag
     */
    const CACHE_TAG = 'carhartt_stock_notification';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(NotificationResource::class);
    }

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Config $configHelper
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        Context                         $context,
        Registry                        $registry,
        protected Config                $configHelper,
        protected StoreManagerInterface $storeManager,
        protected TransportBuilder      $transportBuilder,
        ?AbstractResource               $resource = null,
        ?AbstractDb                     $resourceCollection = null,
        array                           $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->getData(self::ENTITY_ID) ? (int)$this->getData(self::ENTITY_ID) : null;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID)
            ? (int)$this->getData(self::CUSTOMER_ID)
            : null;
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return (string)$this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->getData(self::PRODUCT_ID)
            ? (int)$this->getData(self::PRODUCT_ID)
            : null;
    }

    /**
     * @return string
     */
    public function getProductSku(): string
    {
        return (string)$this->getData(self::PRODUCT_SKU);
    }

    /**
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID)
            ? (int)$this->getData(self::STORE_ID)
            : null;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    // --- Setters ---

    /**
     * @param int $entityId
     * @return self
     */
    public function setEntityId($entityId): self
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @param int|null $customerId
     * @return self
     */
    public function setCustomerId(?int $customerId): self
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @param string $email
     * @return self
     */
    public function setCustomerEmail(string $email): self
    {
        return $this->setData(self::CUSTOMER_EMAIL, $email);
    }

    /**
     * @param int $productId
     * @return self
     */
    public function setProductId(int $productId): self
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @param string $sku
     * @return self
     */
    public function setProductSku(string $sku): self
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    /**
     * @param int $storeId
     * @return self
     */
    public function setStoreId(int $storeId): self
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
