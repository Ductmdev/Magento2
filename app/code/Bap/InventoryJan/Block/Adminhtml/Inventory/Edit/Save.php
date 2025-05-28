<?php
namespace Bap\InventoryJan\Block\Adminhtml\Inventory\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Save implements ButtonProviderInterface
{
    protected UrlInterface $urlBuilder;
    protected Context $context;

    public function __construct(UrlInterface $urlBuilder, Context $context)
    {
        $this->urlBuilder = $urlBuilder;
        $this->context = $context;
    }

    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ]
        ];
    }
}