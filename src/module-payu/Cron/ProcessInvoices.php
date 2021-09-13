<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Cron;

use Eloom\PayU\Model\InvoiceFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

class ProcessInvoices {
	
	private $invoiceFactory;
	
	private $orderFactory;
	
	private $logger;
	
	/**
	 * Core event manager proxy
	 *
	 * @var ManagerInterface
	 */
	protected $eventManager = null;
	
	public function __construct(LoggerInterface  $logger,
	                            InvoiceFactory   $invoiceFactory,
	                            OrderFactory     $orderFactory,
	                            ManagerInterface $eventManager) {
		$this->logger = $logger;
		$this->invoiceFactory = $invoiceFactory;
		$this->orderFactory = $orderFactory;
		$this->eventManager = $eventManager;
	}
	
	public function execute() {
		$collection = $this->invoiceFactory->create()->getCollection();
		
		if (count($collection)) {
			foreach ($collection as $invoice) {
				try {
					$order = $this->orderFactory->create()->loadByIncrementId($invoice->getIncrementId());
					
					$this->eventManager->dispatch('eloom_payu_invoice_create', [
							'store_id' => $order->getStoreId(),
							'order_id' => $order->getId()
						]
					);
					
					$invoice->delete();
				} catch (\Exception $e) {
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getTraceAsString()));
				}
			}
		}
	}
}