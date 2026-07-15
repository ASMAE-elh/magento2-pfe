<?php
namespace WB\OneStepCheckout\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class CustomNote extends Template
{
    const XML_PATH_CUSTOM_NOTE = 'wb_onestepcheckout/general/custom_note';
    const XML_PATH_ENABLE = 'wb_onestepcheckout/general/enable';

    protected $_scopeConfig;

    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    public function getCustomNote()
    {
        if ($this->isModuleEnabled()) {
            return $this->_scopeConfig->getValue(self::XML_PATH_CUSTOM_NOTE, ScopeInterface::SCOPE_STORE);
        }
        return '';
    }

    public function isModuleEnabled()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE);
    }
}
?>
