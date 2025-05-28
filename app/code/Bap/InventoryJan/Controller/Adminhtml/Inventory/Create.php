<?php

namespace Bap\InventoryJan\Controller\Adminhtml\Inventory;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Create extends Action
{
    protected PageFactory $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Bap_InventoryJan::inventory');
        $resultPage->getConfig()->getTitle()->prepend(__('New JAN stock'));
        return $resultPage;
    }
}
