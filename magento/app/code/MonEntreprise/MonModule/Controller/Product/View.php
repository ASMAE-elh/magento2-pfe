<?php
namespace MonEntreprise\MonModule\Controller\Product;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

class View implements HttpGetActionInterface
{
    public function __construct(
        private PageFactory $pageFactory,
        private RequestInterface $request,
        private EventManager $eventManager
    ) {}

    public function execute()
    {
        $productId = (int) $this->request->getParam('id');

        // On dispatche notre propre event !
        $this->eventManager->dispatch('monmodule_product_viewed', [
            'product_id' => $productId
        ]);

        $page = $this->pageFactory->create();
        return $page;
    }
}
