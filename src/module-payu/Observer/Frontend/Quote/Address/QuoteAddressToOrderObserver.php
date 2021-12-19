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

namespace Eloom\PayU\Observer\Frontend\Quote\Address;

use Eloom\PayU\Api\Data\OrderPaymentPayUInterface;
use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class QuoteAddressToOrderObserver implements ObserverInterface {
	
	private $logger;
	
	private $orderRepository;
	
	protected $objectCopyService;
	
	public function __construct(OrderRepositoryInterface $orderRepository,
	                            Copy $objectCopyService,
	                            LoggerInterface $logger) {
		$this->orderRepository = $orderRepository;
		$this->objectCopyService = $objectCopyService;
		$this->logger = $logger;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$order = $observer->getEvent()->getData('order');
		$quote = $observer->getEvent()->getData('quote');
		
		$shippingAddressData = $quote->getShippingAddress()->getData();
		if (isset($shippingAddressData[OrderPaymentPayUInterface::PAYU_INTEREST_AMOUNT])) {
			$order->setPayuInterestAmount($shippingAddressData[OrderPaymentPayUInterface::PAYU_INTEREST_AMOUNT]);
			$order->setPayuBaseInterestAmount($shippingAddressData[OrderPaymentPayUInterface::PAYU_BASE_INTEREST_AMOUNT]);
			
			$order->setGrandTotal($order->getGrandTotal() + $order->getPayuInterestAmount());
			$order->setBaseGrandTotal($order->getBaseGrandTotal() + $order->getPayuBaseInterestAmount());
		}
		if (isset($shippingAddressData[OrderPaymentPayUInterface::PAYU_DISCOUNT_AMOUNT])) {
			$order->setPayuDiscountAmount($shippingAddressData[OrderPaymentPayUInterface::PAYU_DISCOUNT_AMOUNT]);
			$order->setPayuBaseDiscountAmount($shippingAddressData[OrderPaymentPayUInterface::PAYU_BASE_DISCOUNT_AMOUNT]);
			
			$order->setGrandTotal($order->getGrandTotal() + $order->getPayuDiscountAmount());
			$order->setBaseGrandTotal($order->getBaseGrandTotal() + $order->getPayuBaseDiscountAmount());
		}
		
		return $this;
	}
	
}