<?php
namespace WB\OneStepCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;

class LayoutLoadBefore implements ObserverInterface
{
    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $isEnabled = $this->scopeConfig->isSetFlag(
            'wb_onestepcheckout/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$isEnabled) {
            /** @var LayoutInterface $layout */
            $layout = $observer->getLayout();
            $layout->unsetElement('wb.checkout.custom.js');
        }
    }
}
