<?php
namespace MonEntreprise\MonModule\Controller\Note;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Create implements HttpGetActionInterface
{
    public function __construct(
        private PageFactory $pageFactory
    ) {}

    public function execute()
    {
        return $this->pageFactory->create();
    }
}
