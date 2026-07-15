<?php
namespace MonEntreprise\MonModule\Model;

class Product extends \Magento\Catalog\Model\Product
{
    public function getCustomLabel(): string
    {
        return 'Produit personnalisé : ' . $this->getName();
    }
}
