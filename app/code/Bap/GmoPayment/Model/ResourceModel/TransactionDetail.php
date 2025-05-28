<?php

namespace Bap\GmoPayment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TransactionDetail extends AbstractDb
{
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_init('gmo_transaction_details', 'entity_id');
    }
}
