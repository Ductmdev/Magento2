<?php

namespace Bap\Register\Model\ResourceModel;

class TempCustomer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('temp_customer', 'id');
    }
}
