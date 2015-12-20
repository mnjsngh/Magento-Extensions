<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('productquestions')} ADD `question_store_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER `question_product_id` ;

");

$installer->endSetup();
?>
