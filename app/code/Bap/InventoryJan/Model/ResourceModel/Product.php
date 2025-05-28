<?php
namespace Bap\InventoryJan\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Product extends AbstractDb
{
    public function __construct(Context $context)
	{
		parent::__construct($context);
	}

    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }
}
