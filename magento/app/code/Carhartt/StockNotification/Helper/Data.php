<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Helper;

use Carhartt\StockNotification\Api\Data\NotificationInterface;
use Carhartt\StockNotification\Model\ResourceModel\Notification;
use Carhartt\StockNotification\Model\ResourceModel\Notification\Collection;
use Carhartt\StockNotification\Helper\Config;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Phrase;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableType;
use Psr\Log\LoggerInterface;

/**
 * Data class
 */
class Data extends AbstractHelper
{
    /**
     * Data constructor.
     * @param Context $context
     * @param ConfigHelper $configHelper
     * @param Config $config
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param Emulation $emulation
     * @param Notification $resourceStockNotification
     * @param Collection $collectionStockNotification
     * @param Product $resourceProduct
     * @param ProductRepositoryInterface $productRepository
     * @param ProductOptionNameService $productOptionNameService
     * @param ResourceConnection $resourceConnection
     * @param ConfigurableType $configurableType
     * @param LoggerInterface $notificationLogger
     * @param int $limit
     */
    public function __construct(
	    Context                              $context,
	    protected Config                     $config,
        protected TransportBuilder           $transportBuilder,
        protected StoreManagerInterface      $storeManager,
        protected Emulation                  $emulation,
        protected Notification               $resourceStockNotification,
        protected Collection                 $collectionStockNotification,
        protected Product                    $resourceProduct,
        protected ProductRepositoryInterface $productRepository,
        protected ResourceConnection         $resourceConnection,
        protected ConfigurableType           $configurableType,
        protected LoggerInterface            $notificationLogger,
        private int $limit = 0

    ) {
        parent::__construct($context);
    }

    /**
     * @param $product
     * @param $toEmail
     * @param $storeId
     * @return void
     * @throws MailException
     */
    public function sendEmail($product, $toEmail, $storeId)
    {
        $templateId = $this->config->getEmailTemplateIdProductIsSaleable($storeId);
        $fromEmail = "elhlaissieasmae@gmail.com";
        $fromName = "Asmae";
        try {
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND);
            $productUrl = $product->getProductUrl();
            $productImage= $product->getImage();
            $colorDescription= $product->getData('colour_description');
            $itemNumber =  $product->getData('item_number');
            $fabricName = $product->getData('material_short_description');
            $colorCode = '';
            $finishCode = '';
            $this->emulation->stopEnvironmentEmulation();

            $dataObject = new DataObject();
            $dataObject->setData([
                'product_name' => $product->getName(),
                'product_url'  => $productUrl,
                'product_image' => $productImage,
                'material_short_description'=> $fabricName,
                'item_number'  => $itemNumber,
                'colour_description'=> $colorDescription,
                'colour_code'=> $colorCode,
                'finish_code'=> $finishCode,
            ]);

            $from = ['email' => $fromEmail, 'name' => $fromName];

            $storeScope = ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars(['data' => $dataObject])
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            throw new MailException(new Phrase($e->getMessage()), $e);
        }
    }

    /**
     * @param $product
     * @param $toEmail
     * @param $storeId
     * @return void
     * @throws MailException
     */
    public function sendConfirmationEmail($product, $toEmail, $storeId)
    {
        $templateId = $this->config->getEmailTemplateIdSubscriptionConfirmation($storeId);
        $fromEmail = "elhlaissieasmae@gmail.com";
        $fromName = "Asmae";
        try {
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND);
            $productUrl = $product->getProductUrl();
            $productImage = $product->getImage();
            $colorDescription = $product->getData('colour_description');
            $itemNumber = $product->getData('item_number');
            $fabricName = $product->getData('material_short_description');
            $colorCode = $product->getData('colour_code');
            $finishCode = $product->getData('finish_code');
            $this->emulation->stopEnvironmentEmulation();

            $dataObject = new DataObject();
            $dataObject->setData([
                'product_name' => $product->getName(),
                'product_url'  => $productUrl,
                'product_image' => $productImage,
                'material_short_description' => $fabricName,
                'item_number'  => $itemNumber,
                'colour_description' => $colorDescription,
                'colour_code' => $colorCode,
                'finish_code' => $finishCode,
            ]);

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $storeScope = ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars(['data' => $dataObject])
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            throw new MailException(new Phrase($e->getMessage()), $e);
        }
    }

    /**
     * @param array|null $productIds
     * @return void
     * @throws AlreadyExistsException
     */
    public function sendEmailToCustomer(?array $productIds = null): void
    {
        $collection = $this->getStockNotificationItems($productIds);
        foreach ($collection as $_item) {
            $storeId = $_item->getStoreId();

            if (!$this->config->isStockNotificationEnable($storeId)) {
                continue;
            }

            try {
                $product = $this->productRepository->getById($_item->getProductId(), false, $storeId, true);
            } catch (\Exception $e) {
                $this->notificationLogger->error("Retrieve Product Mail Error: " . $e->getMessage());
                continue;
            }

            if (!$product || !$_item->getCustomerEmail()) {
                continue;
            }

            if (!$product->isSaleable()) {
                continue;
            }

            try {
                $this->sendEmail($product, $_item->getCustomerEmail(), $storeId);
                $this->notificationLogger->info(sprintf("Send stock notification of product %s to %s",
                    $product->getName(),
                    $_item->getCustomerEmail()
                ));
            } catch (MailException $e) {
                $this->notificationLogger->error("Mail Error: " . $e->getMessage());
                continue;
            }

            $this->notificationLogger->info(sprintf("Mail sent to %s for product %s", $_item->getCustomerEmail(), $product->getName()));

            $_item->setStatus(NotificationInterface::NOTIFICATION_STATUS_CLOSED);
            $this->resourceStockNotification->save($_item);
        }
    }

    /**
     * Get product qty direct from cataloginventory_stock_item
     * @param $productId
     * @return float
     */
    private function getProductQty($productId): float
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('cataloginventory_stock_item');

        $select = $connection->select()
            ->from($table, 'qty')
            ->where('product_id = ?', $productId);
        $this->notificationLogger->info("Product qty: " . $select->__toString());
        return (float) $connection->fetchOne($select);
    }

    /**
     * @param null $parentIds
     * @return Collection
     */
    public function getStockNotificationItems($parentIds = null): Collection
    {
        $collection = $this->collectionStockNotification
            ->addFieldToFilter('status', ['eq' => NotificationInterface::NOTIFICATION_STATUS_PENDING]);
        if ($parentIds) {
            $collection->addFieldToFilter('product_id', ['in' => $parentIds]);
        }

        if ($this->limit) {
            $collection->setPageSize($this->limit);
        }
        return $collection;
    }
}
