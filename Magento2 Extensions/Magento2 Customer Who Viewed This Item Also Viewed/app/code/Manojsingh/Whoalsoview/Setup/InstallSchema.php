<?php namespace Manojsingh\Whoalsoview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('who_also_view'))
            ->addColumn(
                'id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn('product_sku', Table::TYPE_TEXT, 255, ['nullable' => false],'Product Sku')
            ->addColumn('product_categories', Table::TYPE_TEXT, 255, ['nullable' => false],'Product Categories')
            ->addColumn('session_cod', Table::TYPE_TEXT, 255, ['nullable' => false], 'Session code')
            ->addColumn('product_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Product Id')
            ->addColumn('ip', Table::TYPE_TEXT, 255, ['nullable' => false], 'Ip')
            ->setComment('Whoalsoview table');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
