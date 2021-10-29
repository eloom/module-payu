<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.3
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Setup;

use Eloom\Payment\Api\Data\OrderPaymentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Store\Model\StoreManagerInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	public function __construct(StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig) {
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
	}

	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		$setup->endSetup();
	}
}
