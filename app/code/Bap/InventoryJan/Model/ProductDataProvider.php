<?php
namespace Bap\InventoryJan\Model;

use Bap\InventoryJan\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
 
class ProductDataProvider extends AbstractDataProvider
{

    /**
     * @var array
     */
    protected array $loadedData = [];

    protected RequestInterface $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RequestInterface $request,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->request = $request;
        $sourceItemId = $this->request->getParam('id');
        $this->collection = $collectionFactory->create([
            'sourceItemId' => $sourceItemId
        ]);
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
}