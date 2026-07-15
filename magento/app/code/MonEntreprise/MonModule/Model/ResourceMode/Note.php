<?php
namespace MonEntreprise\MonModule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Note extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('monentreprise_note', 'note_id');
    }
}
