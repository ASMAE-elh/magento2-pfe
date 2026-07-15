<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Controller\Ajax;

use Carhartt\StockNotification\Api\Data\NotificationInterface;
use Carhartt\StockNotification\Model\ResourceModel\Notification;
use Carhartt\StockNotification\Model\ResourceModel\Notification\Collection;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Carhartt\StockNotification\Model\NotificationFactory;
use Carhartt\StockNotification\Helper\Config as ConfigHelper;
use Carhartt\StockNotification\Helper\Data as DataHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Submit notification class
 */
class Submit extends Action
{
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param NotificationFactory $notificationFactory
     * @param ConfigHelper $configHelper
     * @param FormKey $formKey
     * @param Product $resourceProduct
     * @param ProductInterface $product
     * @param StoreManagerInterface $storeManager
     * @param Collection $collectionStockNotification
     * @param NotificationInterface $stockNotification
     * @param Notification $resourceStockNotification
     * @param CustomerSession $customerSession
     * @param LoggerInterface $notificationLogger
     */
    public function __construct(
        Context                                                              $context,
        protected JsonFactory                                                $resultJsonFactory,
        protected NotificationFactory                                        $notificationFactory,
        protected ConfigHelper                                               $configHelper,
        protected DataHelper                                                 $dataHelper,
        protected FormKey                                                    $formKey,
        protected Product                                                    $resourceProduct,
        protected ProductInterface                                           $product,
        protected StoreManagerInterface                                      $storeManager,
        protected Collection                                                 $collectionStockNotification,
        protected NotificationInterface                                      $stockNotification,
        protected Notification                                               $resourceStockNotification,
        protected CustomerSession                                            $customerSession,
        protected LoggerInterface                                            $notificationLogger
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws NotFoundException
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Page not found.'));
        }

        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getParams();
        $customerId = $this->customerSession->getCustomerId();
        $error = false;
        if (!isset($data['product_id'], $data['form_key'])
            || !$data['product_id'] || !$data['form_key'] || !$customerId) {
            $error = true;
            $result = [
                'error' => 1,
                'message' => 'You have to submit a valid email address and You have to select a product.'
            ];
            $this->notificationLogger->critical('Submit form without data');
        }

        $formKey = $data['form_key'] ?? '';

        if (!$error && $this->formKey->getFormKey() !== $formKey) {
            $error = true;
            $result = [
                'error' => 1,
                'message' => 'There has been an error while submitting your data. Please try again later.'
            ];
            $this->notificationLogger->critical('Form with invalid form key: ' . $formKey);
        }

        if (!$error) {
            // check if product is unavailable
            $this->resourceProduct->load($this->product, $data['product_id']);
            $storeId = $this->storeManager->getStore()->getId();
            $productSku = $this->product->getSku();
            $stockNotificationCount = $this->collectionStockNotification
                ->addFieldToFilter('product_id', $data['product_id'])
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('status', ['IN' =>
                    [NotificationInterface::NOTIFICATION_STATUS_PENDING]
                ])->getSize();

            if ($stockNotificationCount > 0) {
                $result = [
                    'error' => 1,
                    'message' => __('You have previously registered for this product..')
                ];
            } else if (!$this->product->getId()) {
                $result = [
                    'error' => 1,
                    'message' => __('There has been an error while loading the product please try again later.')
                ];
            } else if ($stockNotificationCount === 0) {
                $this->stockNotification->setData(
                    'product_id', $data['product_id']
                )->setData(
                    'customer_id', $customerId
                )->setData(
                    'product_sku', $productSku
                )->setData(
                    'customer_email', $this->customerSession->getCustomer()->getEmail()
                )->setData(
                    'store_id', $storeId
                )->setData(
                    'status',
                    NotificationInterface::NOTIFICATION_STATUS_PENDING,
                );
                $this->resourceStockNotification->save($this->stockNotification);
                $this->notificationLogger->info('new subscriber to notification for product: ' . $this->product->getId());

                try {
                    $this->dataHelper->sendConfirmationEmail(
                        $this->product,
                        $this->customerSession->getCustomer()->getEmail(),
                        $storeId
                    );
                } catch (\Exception $e) {
                    $this->notificationLogger->error('Confirmation mail error: ' . $e->getMessage());
                }
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
            'result' => $result
        ]);
    }
}
