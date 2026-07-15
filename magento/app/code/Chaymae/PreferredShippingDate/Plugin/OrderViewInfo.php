<?php
namespace Chaymae\PreferredShippingDate\Plugin;

use Magento\Sales\Block\Adminhtml\Order\View\Info;

class OrderViewInfo
{
    public function afterToHtml(Info $subject, string $result): string
    {
        $order = $subject->getOrder();
        $date  = $order->getData('preferred_shipping_date');

        if ($date) {
            $result .= '<section class="admin__page-section">
                <div class="admin__page-section-title">
                    <span class="title">Preferred Shipping Date</span>
                </div>
                <div class="admin__page-section-content">
                    <p><strong>' . htmlspecialchars($date) . '</strong></p>
                </div>
            </section>';
        }

        return $result;
    }
}
