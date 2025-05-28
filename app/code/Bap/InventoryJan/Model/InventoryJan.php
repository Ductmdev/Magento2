<?php
namespace Bap\InventoryJan\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class InventoryJan extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'bap_inventory_jan';

    protected $_cacheTag = 'bap_inventory_jan';

    protected $_eventPrefix = 'bap_inventory_jan';

    protected $_idFieldName = 'source_item_id';

    protected function _construct()
    {
        $this->_init(\Bap\InventoryJan\Model\ResourceModel\InventoryJan::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
