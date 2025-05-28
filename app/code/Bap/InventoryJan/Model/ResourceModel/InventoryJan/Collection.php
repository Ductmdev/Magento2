<?php
namespace Bap\InventoryJan\Model\ResourceModel\InventoryJan;

use Bap\InventoryJan\Model\ResourceModel\InventoryJan;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;


class Collection extends SearchResult
{

     /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $mainTable = 'bap_inventory_source_item_jan',
        $resourceModel = InventoryJan::class,
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
       
        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
        $catalogProductEntity = $this->getTable('catalog_product_entity');
        $catalogProductEntityVarchar = $this->getTable('catalog_product_entity_varchar');
        $eavAttribute = $this->getTable('eav_attribute');
        $eavAttributeOptionValue = $this->getTable('eav_attribute_option_value');
        $catalogProductWebsite = $this->getTable('catalog_product_website');

        $subqueryProduct = "
        SELECT tem.name, tem.product_type, tem.sku, tem.entity_id, tem.bap_jancode as 'jancode', sub.set_item FROM (
            SELECT cpe.entity_id, cpe.sku, cpe.created_at, cpev.value as 'bap_jancode', cpev1.value as 'name', eaov.value as 'product_type' FROM {$catalogProductEntity} cpe 
            JOIN {$catalogProductEntityVarchar}  cpev ON cpev.entity_id = cpe.entity_id AND cpev.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'bap_jancode' AND ea.entity_type_id = 4)
            JOIN {$catalogProductEntityVarchar} cpev1 ON cpev1.entity_id = cpe.entity_id AND cpev1.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'name' AND ea.entity_type_id = 4)
            JOIN {$catalogProductEntityVarchar} cpev2 ON cpev2.entity_id = cpe.entity_id AND cpev2.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'product_type' AND ea.entity_type_id = 4)
            JOIN {$eavAttributeOptionValue} eaov ON eaov.option_id = cpev2.value
            JOIN {$catalogProductWebsite} cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = {$currentWebsiteId}
            ORDER BY cpev.value ASC, eaov.value ASC,created_at ASC
        ) as tem
        JOIN (
            SELECT IF(COUNT(tem2.product_type) > 0, 1, 0) as set_item, tem2.bap_jancode, COALESCE(MIN(CASE WHEN product_type IS NULL THEN created_at END), MIN(CASE WHEN product_type = 'Set Product' THEN created_at END)) AS min_created_at
            FROM (
                SELECT eaov1.value as 'product_type', cpe.created_at, cpev.value as 'bap_jancode' FROM {$catalogProductEntity} cpe
                JOIN {$catalogProductEntityVarchar}  cpev ON cpev.entity_id = cpe.entity_id AND cpev.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'bap_jancode' AND ea.entity_type_id = 4)
                JOIN {$catalogProductEntityVarchar} cpev2 ON cpev2.entity_id = cpe.entity_id AND cpev2.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'product_type' AND ea.entity_type_id = 4)
                JOIN {$eavAttributeOptionValue} eaov ON eaov.option_id = cpev2.value
                JOIN {$catalogProductWebsite} cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = {$currentWebsiteId}
                LEFT JOIN {$eavAttributeOptionValue} eaov1 ON eaov1.option_id = cpev2.value AND eaov1.value = 'Set Product'
            ) as tem2
            Group by tem2.bap_jancode
        ) as sub ON tem.bap_jancode = sub.bap_jancode AND tem.created_at = sub.min_created_at Group by tem.bap_jancode
        ";

        $this->getSelect()->joinLeft(
            ['product' => new \Zend_Db_Expr("({$subqueryProduct})")],
            'product.jancode = main_table.bap_jancode',
            ['name', 'product_type', 'set_item']
        );

        return $this;
    }
}
