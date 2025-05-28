<?php

namespace Bap\GmoPayment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install DB Schema
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('gmo_transaction_details')) {
            $table = $installer->getConnection()->newTable($installer->getTable('gmo_transaction_details'))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'access_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Access ID'
                )
                ->addColumn(
                    'access_pass',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Access Pass'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Order ID'
                )
                ->addIndex(
                    $installer->getIdxName('gmo_transaction_details', ['order_id']),
                    ['order_id']
                )
                ->addForeignKey(
                    $installer->getFkName('gmo_transaction_details', 'order_id', 'sales_order', 'entity_id'),
                    'order_id',
                    $installer->getTable('sales_order'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('GMO Transaction Details Table');

            $installer->getConnection()->createTable($table);

            $installer->endSetup();
        }
    }
}
