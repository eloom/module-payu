<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.0
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Setup;

use Eloom\PayU\Api\Data\OrderPaymentPayUInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		$connection = $setup->getConnection();

		$tables = ['quote_address', 'sales_order', 'sales_invoice'];
		foreach ($tables as $t) {
			$table = $setup->getTable($t);

			if ($connection->tableColumnExists($table, OrderPaymentPayUInterface::PAYU_DISCOUNT_AMOUNT) === false) {
				$connection->addColumn($table,
					OrderPaymentPayUInterface::PAYU_DISCOUNT_AMOUNT,
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '20,4',
						'comment' => 'PayU Discount Amount'
					]
				);
			}
			if ($connection->tableColumnExists($table, OrderPaymentPayUInterface::PAYU_BASE_DISCOUNT_AMOUNT) === false) {
				$connection->addColumn($table,
					OrderPaymentPayUInterface::PAYU_BASE_DISCOUNT_AMOUNT,
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '20,4',
						'comment' => 'PayU Base Discount Amount'
					]
				);
			}

			if ($connection->tableColumnExists($table, OrderPaymentPayUInterface::PAYU_INTEREST_AMOUNT) === false) {
				$connection->addColumn($table,
					OrderPaymentPayUInterface::PAYU_INTEREST_AMOUNT,
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '20,4',
						'comment' => 'PayU Interest Amount'
					]
				);
			}
			if ($connection->tableColumnExists($table, OrderPaymentPayUInterface::PAYU_BASE_INTEREST_AMOUNT) === false) {
				$connection->addColumn($table,
					OrderPaymentPayUInterface::PAYU_BASE_INTEREST_AMOUNT,
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '20,4',
						'comment' => 'PayU Base Interest Amount'
					]
				);
			}
		}

		$table = $setup->getTable('sales_order_payment');
		if ($connection->tableColumnExists($table, OrderPaymentPayUInterface::TRANSACTION_STATE) === false) {
			$connection->addColumn($table,
				OrderPaymentPayUInterface::TRANSACTION_STATE,
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 30,
					'comment' => 'Transaction State'
				]
			);
		}

		if(!$setup->tableExists('eloom_payu_notification')) {
			$table = $setup->getConnection()->newTable($setup->getTable('eloom_payu_notification'))
				->addColumn(
					'entity_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Entity ID'
				)
				->addColumn(
					'increment_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					32,
					[
						'nullable' => false
					],
					'Order Increment ID'
				);
			$setup->getConnection()->createTable($table);
		}

		$setup->endSetup();
	}
}
