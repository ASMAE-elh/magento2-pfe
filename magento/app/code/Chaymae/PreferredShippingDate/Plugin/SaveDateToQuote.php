<?php
namespace Chaymae\PreferredShippingDate\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class SaveDateToQuote
{
    public function __construct(
        private CartRepositoryInterface $quoteRepository
    ) {}

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        $date = $shippingAddress->getCustomAttribute('preferred_shipping_date');

        if ($date) {
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->getShippingAddress()->setData('preferred_shipping_date', $date->getValue());
            $this->quoteRepository->save($quote);
        }

        return [$cartId, $addressInformation];
    }
}
