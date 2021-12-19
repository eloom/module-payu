<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.4
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Observer;

use Eloom\PayU\Model\InvoiceFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\ManagerInterface;

class OrderSaveAfter implements ObserverInterface {
	
	private $logger;
	
	private $invoiceFactory;
	
	public function __construct(LoggerInterface $logger,
	                            InvoiceFactory  $invoiceFactory) {
		$this->logger = $logger;
		$this->invoiceFactory = $invoiceFactory;
	}
	
	/**
	 * @inheritdoc
	 */
	public function execute(Observer $observer) {
		$order = $observer->getEvent()->getOrder();
		
		$invoice = $this->invoiceFactory->create();
		$invoice->setIncrementId($order->getIncrementId());
		$invoice->save();
	}
}