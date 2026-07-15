<?php

declare(strict_types=1);

namespace MonEntreprise\MonModule\Controller\Map;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Index Class
 */
class Index implements HttpGetActionInterface
{
    /**
     * @param PageFactory $pageFactory
     */
    public function __construct(
        private PageFactory $pageFactory
    ) {}

    /**
     * @return ResultInterface|Page
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }
}
