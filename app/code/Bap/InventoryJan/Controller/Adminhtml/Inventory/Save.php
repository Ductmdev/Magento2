<?php
namespace Bap\InventoryJan\Controller\Adminhtml\Inventory;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Bap\InventoryJan\Model\InventoryJanFactory;
use Magento\Framework\Message\ManagerInterface;

class Save extends Action
{
    protected InventoryJanFactory $inventoryJan;
    protected $messageManager;

    public function __construct(
        Context $context,
        InventoryJanFactory $inventoryJan,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->inventoryJan = $inventoryJan;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost('items', []);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection();

        $tableName = $resource->getTableName('bap_inventory_source_item_jan');
        $sql = "SELECT * FROM $tableName WHERE bap_jancode = :bap_jancode AND source_code = :source_code";
        $bind = [
            'bap_jancode' => $data['bap_jancode'],
            'source_code' => $data['source_code']
        ];

        $result = $connection->fetchRow($sql, $bind);

        $source_item_id = $data['source_item_id'] ?? null;

        if (!$data) {
            $this->messageManager->addErrorMessage(__('No data to save.'));
            return $this->_redirect('*/*/index');
        }

        if(!empty($result['source_item_id']) && $result['source_item_id'] != $data['source_item_id']) {
            $this->messageManager->addErrorMessage(__('Registration is not possible because the same JAN code and warehouse already exist'));
            $formdata = $data;
            unset($formdata['orig_data']);
            $this->_getSession()->setFormData($formdata);
            return $this->_redirect('*/*/edit', ['id' => $data['source_item_id'] ?? null, 'error' => 1]);
        } else if(empty($result['source_item_id'])) {
            $source_item_id= null;
        }

        try {
            $inventory = $this->inventoryJan->create();

            if ($source_item_id) {
                $inventory->load($source_item_id);
                $inventory->setData($data);
            } else {
                unset($data['source_item_id']);
                $inventory->setData($data);
            }

            $inventory->save();

            $this->messageManager->addSuccessMessage(__('Inventory saved successfully.'));

            return $this->_redirect('*/*/edit', ['id' => $inventory['source_item_id']]);

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->_redirect('*/*/edit', ['id' => $data['source_item_id'] ?? null, 'error' => 1]);
        }
    }
}
