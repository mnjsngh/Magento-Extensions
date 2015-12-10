<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('productquestions')} ADD `question_product_name` VARCHAR( 255 ) NOT NULL AFTER `question_product_id` ;

");

$installer->endSetup();
?>
