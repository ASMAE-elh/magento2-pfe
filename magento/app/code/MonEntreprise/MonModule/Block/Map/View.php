<?php
namespace MonEntreprise\MonModule\Block\Map;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class View extends Template
{
    public function __construct(
        Context $context,
        private ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getMapUrl(): string
    {
        return (string) $this->scopeConfig->getValue(
            'monmodule/googlemaps/map_url'
        );
    }
}
