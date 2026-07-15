<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Class
 */
class Config extends AbstractHelper
{
    /** @var string  */
    private const XML_PATH_ENABLED = 'carhartt_stocknotification/general/enabled';

    /** @var string  */
    private const XML_PATH_EMAIL_TEMPLATE_PRODUCT_IS_SALEABLE = 'carhartt_stocknotification/general/template_product_is_saleable';

    /** @var string  */
    private const XML_PATH_EMAIL_TEMPLATE_SUBSCRIPTION_CONFIRMATION = 'carhartt_stocknotification/general/template_subscription_confirmation';

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Check if stock notification feature is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isStockNotificationEnable(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getEmailTemplateIdProductIsSaleable($storeId = null): mixed
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE_PRODUCT_IS_SALEABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getEmailTemplateIdSubscriptionConfirmation($storeId = null): mixed
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE_SUBSCRIPTION_CONFIRMATION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
