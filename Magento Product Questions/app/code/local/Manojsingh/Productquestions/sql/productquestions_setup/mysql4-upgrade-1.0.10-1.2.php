<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();


try {
    $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
    // $oldTableName = $tablePrefix.substr($newTableName, strlen($tablePrefix));
    $oldTableName = $tablePrefix . 'productquestions';

    $newTableName = $this->getTable('productquestions');

    $installer->run("RENAME TABLE $oldTableName TO $newTableName;");
} catch (Exception $e) {
    Mage::log($e);
}

$installer->endSetup();
?>
