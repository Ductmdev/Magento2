<?php

namespace Bap\InventoryJan\Model\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class SourceCodeOptions implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        $connection = $this->resource->getConnection();
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        
        $sql = "
            SELECT 
                isi.source_code, 
                isi.name
            FROM 
                store_website sw
            JOIN 
                inventory_stock_sales_channel issc ON sw.code = issc.code
            JOIN 
                inventory_source_stock_link iss ON issc.stock_id = iss.stock_id
            JOIN 
                inventory_source isi ON iss.source_code = isi.source_code
            WHERE 
                sw.code = :websiteCode AND isi.enabled = 1;
        ";
        
        $binds = ['websiteCode' => $websiteCode];
        $results = $connection->fetchAll($sql, $binds);

        foreach ($results as $row) {
            $options[] = [
                'value' => $row['source_code'],
                'label' =>  __($row['name'])
            ];
        }

        return $options;
    }
}
