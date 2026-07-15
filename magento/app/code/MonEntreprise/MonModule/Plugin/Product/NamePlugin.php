<?php
namespace MonEntreprise\MonModule\Plugin\Product;

use Magento\Catalog\Model\Product;

class NamePlugin
{
    public function afterGetName(Product $subject, $result)
    {
        return $result;
    }
}
