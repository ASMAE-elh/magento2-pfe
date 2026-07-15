<?php
namespace MonEntreprise\MonModule\Model\ResourceModel\Note;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \MonEntreprise\MonModule\Model\Note::class,
            \MonEntreprise\MonModule\Model\ResourceModel\Note::class
        );
    }
}
