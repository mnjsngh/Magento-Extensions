<?php
class Manojsingh_Productquestions_Test_Model_Mocks_Foreignresetter extends Mage_Core_Model_Abstract {

    public static $counter = 0;

    public static function dropForeignKeys() {

        if (!self::$counter) {

            $resource = Mage::getModel('core/resource');
            $connection = $resource->getConnection('core_write');


            $FKscope = array(
                'cataloginventory_stock_status' => array('FK_CATALOGINVENTORY_STOCK_STATUS_STOCK', 'FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE', 'FK_CATINV_STOCK_STS_STOCK_ID_CATINV_STOCK_STOCK_ID'),
                'catalog_product_website' => array('FK_CATALOG_PRODUCT_WEBSITE_WEBSITE'),
                'catalog_product_entity_int' => array('FK_CATALOG_PRODUCT_ENTITY_INT_ATTRIBUTE', 'FK_CATALOG_PRODUCT_ENTITY_INT_STORE', 'FK_CATALOG_PRODUCT_ENTITY_INT_PRODUCT_ENTITY'),
                    //'core_store_group'=> array('FK_CORE_STORE_GROUP_WEBSITE_ID_CORE_WEBSITE_WEBSITE_ID', 'FK_CORE_STORE_GROUP_ID_CORE_STORE_GROUP_GROUP_ID'),
                    //'core_store' => array('FK_CORE_STORE_GROUP_ID_CORE_STORE_GROUP_GROUP_ID')
            );

            foreach ($FKscope as $table => $fks) {
                foreach ($fks as $fk) {
                    try {
                        $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`"));
                        $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP KEY `{$fk}`"));
                    } catch (Exception $e) {
                        
                    }
                }
            }


            self::$counter = 1;
        }
    }

}