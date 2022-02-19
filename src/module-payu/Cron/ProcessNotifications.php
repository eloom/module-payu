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

namespace Eloom\PayU\Cron;

use Eloom\PayU\Model\NotificationFactory;
use Eloom\PayU\Model\PaymentManagement\Processor;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

class ProcessNotifications {
	
	private $notificationFactory;
	
	private $orderFactory;
	
	private $logger;
	
	public function __construct(LoggerInterface $logger,
	                            NotificationFactory $notificationFactory,
	                            OrderFactory $orderFactory) {
		$this->logger = $logger;
		$this->notificationFactory = $notificationFactory;
		$this->orderFactory = $orderFactory;
	}
	
	public function execute() {
		$collection = $this->notificationFactory->create()->getCollection();
		
		if (count($collection)) {
			foreach ($collection as $notification) {
				try {
					$order = $this->orderFactory->create()->loadByIncrementId($notification->getIncrementId());
					
					if ($order && $order->getId() && !$order->isCanceled()) {
						$this->logger->info(sprintf("%s - Processing PayU notification. Order [%s].", __METHOD__, $notification->getIncrementId()));
						
						$processor = ObjectManager::getInstance()->get(Processor::class);
						$processor->syncronize($order->getPayment(), false, $order->getGrandTotal());
					}
					$notification->delete();
				} catch (\Exception $e) {
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getTraceAsString()));
				}
			}
		}
	}
}