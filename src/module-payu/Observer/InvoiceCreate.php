<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.5
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Observer;

use Eloom\PayU\Gateway\Config\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Psr\Log\LoggerInterface;

class InvoiceCreate implements ObserverInterface {
	
	private $logger;
	
	/**
	 * @var Config
	 */
	private $config;
	
	protected $orderRepository;
	
	protected $invoiceService;
	
	protected $transaction;
	
	protected $invoiceSender;
	
	public function __construct(LoggerInterface          $logger,
	                            Config                   $config,
	                            OrderRepositoryInterface $orderRepository,
	                            InvoiceService           $invoiceService,
	                            InvoiceSender            $invoiceSender,
	                            Transaction              $transaction) {
		$this->logger = $logger;
		$this->config = $config;
		$this->orderRepository = $orderRepository;
		$this->invoiceService = $invoiceService;
		$this->transaction = $transaction;
		$this->invoiceSender = $invoiceSender;
	}
	
	/**
	 * @inheritdoc
	 */
	public function execute(Observer $observer) {
		$storeId = $observer->getEvent()->getStoreId();
		
		if ($this->config->isInvoiceCreate($storeId)) {
			$orderId = $observer->getEvent()->getOrderId();
			$this->logger->info(__("New Invoice for Order #%1.", $orderId));
			
			$order = $this->orderRepository->get($orderId);
			if ($order->canInvoice()) {
				$invoice = $this->invoiceService->prepareInvoice($order);
				if (!$invoice) {
					throw new LocalizedException(__('We can\'t save the invoice for order #%1.', $orderId));
				}
				if (!$invoice->getTotalQty()) {
					throw new LocalizedException(__('You can\'t create an invoice without products for order #%1.', $orderId));
				}
				$invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
				$invoice->register();
				$invoice->getOrder()->setCustomerNoteNotify(false);
				$invoice->getOrder()->setIsInProcess(true);
				$invoice->pay()->save();
				
				$transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
				$transactionSave->save();
				
				try {
					$this->invoiceSender->send($invoice);
				} catch (\Exception $e) {
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getTraceAsString()));
				}
				
				$order->addStatusHistoryComment(__("New Invoice for Order #%1.", $orderId))
					->setIsCustomerNotified(true)
					->save();
			}
		}
	}
}