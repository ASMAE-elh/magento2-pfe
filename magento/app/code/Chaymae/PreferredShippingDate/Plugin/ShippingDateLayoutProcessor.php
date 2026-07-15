<?php
namespace Chaymae\PreferredShippingDate\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ShippingDateLayoutProcessor
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {}

    public function afterProcess($subject, array $jsLayout): array
    {
        $minDays = (int)$this->scopeConfig->getValue(
            'shipping/preferred_date/min_days',
            ScopeInterface::SCOPE_STORE
        ) ?: 5;

        $minDate = date('m/d/Y', strtotime("+{$minDays} days"));

        $jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['shippingAddress']['children']
        ['shipping-address-fieldset']['children']['preferred_shipping_date'] = [
            'component'   => 'Magento_Ui/js/form/element/date',
            'config'      => [
                'customScope' => 'shippingAddress',
                'template'    => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/date',
                'options'     => [
                    'minDate'     => $minDate,
                    'dateFormat'  => 'MM/dd/yyyy',
                    'changeMonth' => true,
                    'changeYear'  => true,
                ],
                'id' => 'preferred_shipping_date',
            ],
            'dataScope'  => 'shippingAddress.preferred_shipping_date',
            'label'      => __('Preferred Delivery Date'),
            'provider'   => 'checkoutProvider',
            'sortOrder'  => 250,
            'visible'    => true,
            'validation' => ['required-entry' => false],
            'id'         => 'preferred_shipping_date',
        ];

        return $jsLayout;
    }
}
