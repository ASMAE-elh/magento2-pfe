<?php
namespace Chaymae\CustomerAvatar\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

class Avatar extends Template
{
    public function __construct(
        Template\Context $context,
        private Session $customerSession,
        private StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getAvatarUrl(): string
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }

        $customer = $this->customerSession->getCustomer();
        $avatar = $customer->getData('avatar');

        if ($avatar) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            return $baseUrl . $avatar;
        }

        return '';
    }

    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getUploadUrl(): string
    {
        return $this->getUrl('customeravatar/account/upload');
    }

    public function getCustomerName(): string
    {
        return $this->customerSession->getCustomer()->getName();
    }
}
