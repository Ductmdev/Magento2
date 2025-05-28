<?php
namespace Bap\InventoryJan\Controller\Adminhtml\Inventory;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Bap\InventoryJan\Model\ResourceModel\InventoryJan\CollectionFactory;
 
class Edit extends Action
{
    protected PageFactory $resultPageFactory;
    protected CollectionFactory $collectionFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->collectionFactory = $collectionFactory;
    }
 
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $title = __('New Inventory JAN');

        if ($id) {
            $collection = $this->collectionFactory->create();
            $inventory = $collection->getItemById($id);

            if ($inventory && $inventory->getName()) {
                $title = __($inventory->getName());
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Bap_InventoryJan::inventory');
        $resultPage->getConfig()->getTitle()->prepend($title);
 
        return $resultPage;
    }
}
 
 