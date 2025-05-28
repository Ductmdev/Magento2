<?php

namespace Bap\InventoryJan\Controller\Adminhtml\Inventory;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Bap\InventoryJan\Model\InventoryJanFactory;

class Store extends Action
{
    public function __construct(
        Context $context,
        protected InventoryJanFactory $inventoryJan,
        protected ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $errorMessage = null;
        $inputs = $this->getRequest()->getPostValue();
        
        if (empty($inputs['bap_jancode'])) {
            $errorMessage = __('JAN Code is required');
            goto redirect;
        }

        if (!empty($inputs)) {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $connection->getTableName('bap_inventory_source_item_jan');

            $sql = "SELECT * FROM $tableName WHERE bap_jancode = :bap_jancode AND source_code = :source_code";
            $bind = [
                'bap_jancode' => @$inputs['bap_jancode'],
                'source_code' => @$inputs['source_code']
            ];
            $result = $connection->fetchOne($sql, $bind);
            if (!empty($result)) {
                $errorMessage = __('Registration is not possible because the same JAN code and warehouse already exist.');
                goto redirect;
            }

            try {
                $inventory = $this->inventoryJan->create();
                $inputs['quantity'] = !empty($inputs['quantity']) ? (int) $inputs['quantity'] : 0;
                $inventory->setData($inputs);
                $inventory->save();

                $this->messageManager->addSuccessMessage(__('Inventory saved successfully.'));
            } catch (\Exception $exception) {
                $errorMessage = $exception->getMessage();
            }
        }

        redirect:
        if ($errorMessage) {
            $this->messageManager->addErrorMessage($errorMessage ?: __('No data to save.'));
        }
        return $this->_redirect('*/*/create');
    }
}
