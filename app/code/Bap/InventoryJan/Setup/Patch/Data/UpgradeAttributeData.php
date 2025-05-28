<?php

namespace Bap\InventoryJan\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;

class UpgradeAttributeData implements DataPatchInterface, PatchRevertableInterface
{
    private $moduleDataSetup;
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $setup = $this->moduleDataSetup;
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'bap_jancode',
            [
                'type' => 'varchar',
                'label' => 'JAN Code',
                'input' => 'text',
                'required' => true,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_type',
            [
                'type' => 'varchar',
                'label' => 'Product Type',
                'input' => 'select',
                'required' => true,
                'sort_order' => 20,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'option' => [
                    'values' => [
                        'individual_product' => 'Individual Product',
                        'set_product' => 'Set Product'
                    ],
                    'order' => [
                        'individual_product' => 1,
                        'set_product'        => 2
                    ]
                ],
                'default' => 'individual_product'
            ]
        );

        $setup->endSetup();
    }

    public function revert()
    {
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}