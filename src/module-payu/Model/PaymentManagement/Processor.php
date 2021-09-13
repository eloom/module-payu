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

namespace Eloom\PayU\Model\PaymentManagement;

use Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Eloom\PayU\Model\PaymentManagement\Operations\PaymentDetailsOperation;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Psr\Log\LoggerInterface;

class Processor {
	
	private $logger;
	
	protected $paymentDetailsOperation;
	
	private $searchCriteriaBuilder;
	
	/**
	 * @var OrderConfig
	 */
	protected $orderConfig;
	
	public function __construct(LoggerInterface         $logger,
	                            PaymentDetailsOperation $paymentDetailsOperation,
	                            SearchCriteriaBuilder   $searchCriteriaBuilder,
	                            OrderConfig             $orderConfig) {
		
		$this->logger = $logger;
		$this->paymentDetailsOperation = $paymentDetailsOperation;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->orderConfig = $orderConfig;
	}
	
	public function syncronize(OrderPaymentInterface $payment, $isOnline, $amount): OrderPaymentInterface {
		$actualPayuTransactionState = $payment->getPayuTransactionState() ?? PayUTransactionState::PENDING()->key();
		$payment = $this->paymentDetailsOperation->details($payment, $isOnline, $amount);
		$payuOrderStatus = $payment->getPayuOrderStatus();
		
		$this->logger->info(sprintf("%s - Synchronizing - Order [%s] - Status [%s]", __METHOD__, $payment->getOrder()->getIncrementId(), $payuOrderStatus->key()));
		
		if ($payuOrderStatus->isCaptured()) {
			$payment = $this->capturePayment($payment, $actualPayuTransactionState);
		} elseif ($payuOrderStatus->isCancelled()) {
			$payment = $this->cancelPayment($payment);
		}
		
		return $payment;
	}
	
	private function capturePayment(OrderPaymentInterface $payment, string $actualPayuTransactionState): OrderPaymentInterface {
		$this->logger->info(sprintf("%s - Capture initial - Order [%s] - State [%s]", __METHOD__, $payment->getOrder()->getIncrementId(), $payment->getPayuTransactionState()));
		$payment->save();
		
		if (null == $payment->getPayuTransactionState()
			|| '' == $payment->getPayuTransactionState()
			|| PayUTransactionState::PENDING()->key() == $actualPayuTransactionState) {
			
			$message = __('Approved the payment online.') . ' ' . __('Transaction ID: "%1"', $payment->getTransactionId());
			$payment->capture()->getOrder()->addStatusHistoryComment($message, false)->setIsCustomerNotified(true);
			$payment->getOrder()->save();
		}
		
		return $payment;
	}
	
	public function cancelPayment(OrderPaymentInterface $payment): OrderPaymentInterface {
		$payment->save();
		if (!$payment->getOrder()->hasInvoices()) {
			if ($payment->getOrder()->isPaymentReview()) {
				$payment->getOrder()
					->setState(Order::STATE_NEW)
					->setStatus($this->orderConfig->getStateDefaultStatus(Order::STATE_NEW));
			}
			if ($payment->getOrder()->canCancel()) {
				$this->logger->info(sprintf("%s - Order [%s] has been canceled.", __METHOD__, $payment->getOrder()->getIncrementId()));
				$message = __('Canceled order online') . ' ' . __('Transaction ID: "%1"', $payment->getTransactionId());
				
				$payment->getOrder()
					->setActionFlag(Order::ACTION_FLAG_CANCEL, true)
					->addStatusHistoryComment($message, false)->setIsCustomerNotified(true);
				$payment->getOrder()->cancel()->save();
			} else {
				$this->logger->info(sprintf("%s - Order [%s] can not be canceled.", __METHOD__, $payment->getOrder()->getIncrementId()));
			}
		} else {
			$this->logger->info(sprintf("%s - Order [%s] has already an invoice so cannot be canceled.", __METHOD__, $payment->getOrder()->getIncrementId()));
		}
		
		return $payment;
	}
}
