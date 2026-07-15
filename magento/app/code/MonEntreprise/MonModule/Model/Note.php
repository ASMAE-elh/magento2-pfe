<?php
namespace MonEntreprise\MonModule\Model;

use Magento\Framework\Model\AbstractModel;

class Note extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\MonEntreprise\MonModule\Model\ResourceModel\Note::class);
    }
}
