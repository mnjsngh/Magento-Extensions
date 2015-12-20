<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

try {
    $installer->run("

DROP TABLE IF EXISTS {$this->getTable('productquestions')};

CREATE TABLE {$this->getTable('productquestions')} (
`question_id` int(10) unsigned NOT NULL auto_increment,
`question_status` tinyint(2) NOT NULL default '1',
`question_product_id` int(10) unsigned NOT NULL default '0',
`question_store_id` int(11) NOT NULL default '1',
`question_product_name` varchar(255) NOT NULL,
`question_author_name` varchar(255) NOT NULL,
`question_author_email` varchar(255) NOT NULL default '',
`question_date` datetime NOT NULL default '0000-00-00 00:00:00',
`question_text` text NOT NULL,
`question_reply_text` text NOT NULL,
PRIMARY KEY  (`question_id`),
KEY `question_status` (`question_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

");
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
?>
