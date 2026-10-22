<?php
/**
 * Forces the Magento/luma theme when the OneStepCheckout page
 * (Mageplaza_Osc) is dispatched, because that module relies on
 * Knockout.js bindings that are not compatible with the Hyva
 * (Magewire/Alpine.js) frontend used everywhere else on this site.
 */
declare(strict_types=1);

namespace Chaymae\ThemeSwitcher\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\DesignInterface;

class ForceLumaOnCheckout implements ObserverInterface
{
    /**
     * Theme code to use on the checkout page.
     */
    private const CHECKOUT_THEME_CODE = 'Magento/luma';

    public function __construct(
        private readonly DesignInterface $design
    ) {
    }

    public function execute(EventObserver $observer): void
    {
        $this->design->setDesignTheme(self::CHECKOUT_THEME_CODE);
    }
}
