<?php
namespace WB\OneStepCheckout\Controller\Onepage;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Success extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $checkoutSession;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CheckoutSession $checkoutSession,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $isEnabled = $this->scopeConfig->isSetFlag(
            'wb_onestepcheckout/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($isEnabled) {
            $order = $this->checkoutSession->getLastRealOrder();
            if ($order->getStoreId() == 2) { // Check if the store ID is 2 (International store)
                $this->messageManager->addSuccessMessage(__('We will email you our Payment Link & confirm your order after payment.'));
            }
        }

        return $this->resultPageFactory->create();
    }
}
