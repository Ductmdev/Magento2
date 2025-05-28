<?php

namespace Bap\InventoryJan\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class SelectProduct extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']) && is_array($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName . '_html'] = "<button class='button select-product-jancode action-tertiary' 
                        data-name='" . $item['name'] . "' 
                        data-jan_code='" . $item['bap_jancode'] . "'
                        data-entity_id='" . (int) $item['entity_id'] . "'>
                        <span>" . __('Select') . "</span>
                    </button>";
            }
        }

        return $dataSource;
    }
}
