<?php
declare(strict_types=1);

namespace Carhartt\StockNotification\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Index Class
 */
class Index extends Action
{
    /**
     * Index constructor.
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct
    (
        Action\Context $context,
        protected PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog_products');
        $resultPage->getConfig()->getTitle()->prepend(__('Stock Notification Subscriber Listing'));
        return $resultPage;
    }
}
