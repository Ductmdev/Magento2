<?php

namespace Bap\InventoryJan\Model;

use Bap\InventoryJan\Model\ResourceModel\Product\ProductJancodeFIlterCollectionFactory;
use Bap\InventoryJan\Model\ResourceModel\Product\ProductJancodeFIlterCollection;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ProductJancodeFilterDataProvider extends AbstractDataProvider
{
    protected array $loadedData = [];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        private RequestInterface $request,
        ProductJancodeFIlterCollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
        
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        return $data;
    }

    public function addFilter(Filter $filter)
    {
        /** @var ProductJancodeFIlterCollection $collection */
        $collection = $this->getCollection();
        if ($filter->getField() === 'fulltext') {
            $value = $filter->getValue() !== null ? trim($filter->getValue()) : '';
            $collection->addFullTextFilter($value);
        } else {
            $collection->addFieldToFilter(
                $filter->getField(),
                [$filter->getConditionType() => $filter->getValue()]
            );
        }
    }
}