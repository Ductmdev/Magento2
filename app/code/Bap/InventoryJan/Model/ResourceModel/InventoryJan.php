<?php
namespace Bap\InventoryJan\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class InventoryJan extends AbstractDb


{
    public function __construct(Context $context)
	{
		parent::__construct($context);
	}

    protected function _construct()
    {
        $this->_init('bap_inventory_source_item_jan', 'source_item_id');
    }
}
