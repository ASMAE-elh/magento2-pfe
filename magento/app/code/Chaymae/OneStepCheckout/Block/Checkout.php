<?php
namespace Chaymae\OneStepCheckout\Block;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session as CheckoutSession;

class Checkout extends Template
{
    protected $checkoutSession;

    public function __construct(
        Template\Context $context,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    public function getSubtotal()
    {
        return $this->getQuote()->getSubtotal();
    }

    public function getFormKey()
    {
        return $this->_scopeConfig->getValue('general/store_information/name');
    }
}
