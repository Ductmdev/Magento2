<?php
namespace Bap\InventoryJan\Model;

use Bap\InventoryJan\Model\ResourceModel\InventoryJan\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\Session;
 
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected array $loadedData = [];

    protected RequestInterface $request;
    protected Session $session;

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
        Session $session,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->session = $session;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
 
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $formData = $this->session->getFormData();
        $error = $this->request->getParam('error');
        if (!$this->loadedData) {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $data = $item->getData();
                $data['quantity'] = number_format($data['quantity'], 0);
                if($error==1 && !empty($formData) &&  $formData['source_item_id'] == $item->getId()) {
                    $data = $formData;
                }
                $this->loadedData[$item->getId()] = [
                    'items' => $data
                ];
            }
        }
        $this->session->setFormData(null);
        return $this->loadedData;
    }

    public function getItemById($id)
    {
        return $this->collection->getItemById($id) ?: null;
    }
}