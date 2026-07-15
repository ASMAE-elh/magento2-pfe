<?php
namespace MonEntreprise\MonModule\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;

class View extends Template
{
    public function __construct(
        Context $context,
        private ProductRepositoryInterface $productRepository,
        private RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getProductId(): int
    {
        return (int) $this->request->getParam('id');
    }

    public function getProduct()
    {
        $id = $this->getProductId();
        if (!$id) return null;

        try {
            return $this->productRepository->getById($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}
