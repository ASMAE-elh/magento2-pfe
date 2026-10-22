<?php
/**
 * Registers the Chaymae_ThemeSwitcher module.
 * Forces the Luma theme on the OneStepCheckout page only,
 * because Mageplaza_Osc uses Knockout.js bindings that are
 * incompatible with the Hyva/Magewire frontend.
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Chaymae_ThemeSwitcher',
    __DIR__
);
