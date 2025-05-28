<?php

namespace Bap\InventoryJan\Block\Adminhtml\Inventory\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

abstract class Generic implements ButtonProviderInterface
{
    public function __construct(
        protected Context $context
    ) {
        $this->context = $context;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    public function getModelId()
    {
        return $this->context->getRequest()->getParam('source_item_id');
    }
}
