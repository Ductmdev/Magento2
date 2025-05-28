<?php

namespace Bap\InventoryJan\Model\ResourceModel\Product;

use Bap\InventoryJan\Model\ResourceModel\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class ProductJancodeFIlterCollection extends SearchResult
{
    /**
     * List of fields to fulltext search
     */
    private const FIELDS_TO_FULLTEXT_SEARCH = [
        'name',
        'bap_jancode',
    ];

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        protected StoreManagerInterface $storeManager,
        $mainTable = 'catalog_product_entity',
        $resourceModel = Product::class,
        protected ?string $sourceItemId = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $connection = $this->getConnection();
        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
        
        $mainTable = $this->getMainTable();
        $eavAttribute = $this->getTable('eav_attribute');
        $catalogProductEntityVarchar = $this->getTable('catalog_product_entity_varchar');
        $catalogProductWebsite = $this->getTable('catalog_product_website');
        $eavAttributeOptionValue = $this->getTable('eav_attribute_option_value');

        $attributeIds = $connection->fetchPairs(
            "SELECT attribute_code, attribute_id 
            FROM {$eavAttribute} 
            WHERE attribute_code IN ('bap_jancode', 'name', 'product_type') 
            AND entity_type_id = 4"
        );

        $bapJancodeAttrId = (int) ($attributeIds['bap_jancode'] ?? 0);
        $nameAttrId = (int) ($attributeIds['name'] ?? 0);
        $productTypeAttrId = (int) ($attributeIds['product_type'] ?? 0);

        $subqueryProduct = "
            SELECT temp.entity_id, temp.bap_jancode, temp.name FROM (
                SELECT 
                    cpe.entity_id,
                    cpev.value AS bap_jancode,
                    cpev1.value AS name,
                    ROW_NUMBER() OVER (PARTITION BY cpev.value ORDER BY cpev.value ASC, eaov.value ASC, cpe.created_at ASC) AS row_num
                FROM {$mainTable} cpe 
                JOIN {$catalogProductEntityVarchar} cpev 
                    ON cpev.entity_id = cpe.entity_id AND cpev.attribute_id = {$bapJancodeAttrId}
                JOIN {$catalogProductEntityVarchar} cpev1 
                    ON cpev1.entity_id = cpe.entity_id AND cpev1.attribute_id = {$nameAttrId}
                JOIN {$catalogProductEntityVarchar} cpev2 
                    ON cpev2.entity_id = cpe.entity_id AND cpev2.attribute_id = {$productTypeAttrId}
                JOIN {$eavAttributeOptionValue} eaov 
                    ON eaov.option_id = cpev2.value
                JOIN {$catalogProductWebsite} cpw 
                    ON cpw.product_id = cpe.entity_id AND cpw.website_id = {$currentWebsiteId}
            ) AS temp 
            WHERE temp.row_num = 1
        ";

        $this->getSelect()->join(
            ['temp' => new \Zend_Db_Expr("({$subqueryProduct})")],
            'temp.entity_id = main_table.entity_id',
            ['bap_jancode', 'name', 'entity_id']
        );

        return $this;
    }

    /**
     * Add fulltext filter
     *
     * @param string $value
     * @return $this
     */
    public function addFullTextFilter(string $value)
    {
        $fields = self::FIELDS_TO_FULLTEXT_SEARCH;
        $whereCondition = '';
        foreach ($fields as $key => $field) {
            $condition = $this->_getConditionSql(
                $this->getConnection()->quoteIdentifier($field),
                ['like' => "%$value%"]
            );
            $whereCondition .= ($key === 0 ? '' : ' OR ') . $condition;
        }
        if ($whereCondition) {
            $this->getSelect()->where($whereCondition);
        }

        return $this;
    }
}
