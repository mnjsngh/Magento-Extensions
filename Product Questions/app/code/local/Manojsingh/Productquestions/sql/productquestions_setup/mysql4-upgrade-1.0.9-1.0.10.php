<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('productquestions')}  DEFAULT CHARACTER SET utf8;

ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_author_name` `question_author_name` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_text` `question_text` TEXT CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_reply_text` `question_reply_text` TEXT CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_product_name` `question_product_name` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL;

");

$installer->endSetup();
?>
