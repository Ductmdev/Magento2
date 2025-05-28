<?php

namespace Bap\Register\Model\ResourceModel\TempCustomer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Bap\Register\Model\TempCustomer', 'Bap\Register\Model\ResourceModel\TempCustomer');
    }
}
