<?php
namespace Bap\InventoryJan\Model\ResourceModel\Product;

use Bap\InventoryJan\Model\ResourceModel\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;


class Collection extends SearchResult
{

     /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    protected array $customData = [];

    
    /**
     * @var string|null
     */
    protected ?string $sourceItemId = null;

    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $mainTable = 'catalog_product_entity',
        $resourceModel = Product::class,
        ?string $sourceItemId = null
    ) {
        $this->sourceItemId = $sourceItemId;
        $this->storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();

        $eavAttribute                = $this->getTable('eav_attribute');
        $catalogProductEntityVarchar = $this->getTable('catalog_product_entity_varchar');
        $catalogProductEntityInt     = $this->getTable('catalog_product_entity_int');
        $catalogProductWebsite       = $this->getTable('catalog_product_website');
        $eavAttributeOptionValue     = $this->getTable('eav_attribute_option_value');

        $this->getSelect()->join(
            ['cpev1' => $catalogProductEntityVarchar],
            "cpev1.entity_id = main_table.entity_id AND cpev1.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'bap_jancode' AND ea.entity_type_id = 4)",
            ['bap_jancode' => 'cpev1.value']
        );
       
        $this->getSelect()->join(
            ['cpw' => $catalogProductWebsite],
            "main_table.entity_id = cpw.product_id AND cpw.website_id = {$currentWebsiteId}",
            []
        );

        $this->getSelect()->joinLeft(
            ['cpev' => $catalogProductEntityVarchar],
            "main_table.entity_id = cpev.entity_id AND cpev.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'name' AND ea.entity_type_id = 4)",
            ['name' => 'cpev.value']
        );
        
        $this->getSelect()->joinLeft(
            ['cpev2' => $catalogProductEntityVarchar],
            "main_table.entity_id = cpev2.entity_id AND cpev2.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'product_type' AND ea.entity_type_id = 4)",
            []
        );
        $this->getSelect()->join(
            ['eaov' => $eavAttributeOptionValue],
            "eaov.option_id = cpev2.value",
            ['product_type' => 'eaov.value']
        );
       
        $this->getSelect()->joinLeft(
            ['cpei' => $catalogProductEntityInt],
            "main_table.entity_id = cpei.entity_id AND cpei.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'visibility' AND ea.entity_type_id = 4)",
            ['visibility' => 'cpei.value']
        );

        $this->getSelect()->joinLeft(
            ['cpei1' => $catalogProductEntityInt],
            "main_table.entity_id = cpei1.entity_id AND cpei1.attribute_id = (SELECT attribute_id FROM {$eavAttribute} ea WHERE ea.attribute_code = 'quantity_items' AND ea.entity_type_id = 4)",
            ['quantity_items' => 'cpei1.value']
        );

        return $this;
    }

    protected function _renderFiltersBefore() {
        $connection = $this->getConnection();

        $tableInventory = $this->getTable('bap_inventory_source_item_jan');
        $sqlInventory = "SELECT * FROM $tableInventory WHERE source_item_id = :source_item_id";
        if($this->sourceItemId) {
            $bindInventory = [
                'source_item_id' => $this->sourceItemId
            ];
    
            $inventoryData = $connection->fetchRow($sqlInventory, $bindInventory);
            $bapJancode = $inventoryData['bap_jancode'] ?? null;
    
            $this->getSelect()->where("cpev1.value = ?", $bapJancode);
        }
        parent::_renderFiltersBefore();
    }
}
