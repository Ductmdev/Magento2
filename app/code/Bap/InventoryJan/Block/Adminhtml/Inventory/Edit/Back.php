<?php
namespace Bap\InventoryJan\Block\Adminhtml\Inventory\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Model\UrlInterface;

class Back implements ButtonProviderInterface
{
    protected UrlInterface $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'cancel',
            'sort_order' => 10
        ];
    }

    public function getBackUrl()
    {
        return $this->urlBuilder->getUrl('*/*/index');
    }
}
