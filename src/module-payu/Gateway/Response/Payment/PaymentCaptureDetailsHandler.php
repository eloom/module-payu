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

namespace Eloom\PayU\Gateway\Response\Payment;

use Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;

class PaymentCaptureDetailsHandler implements HandlerInterface {
	
	/**
	 * Core event manager proxy
	 *
	 * @var ManagerInterface
	 */
	protected $eventManager = null;
	
	public function __construct(ManagerInterface $eventManager) {
		$this->eventManager = $eventManager;
	}
	
	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		$result = $response[0]['transaction']->result;
		
		if ($result->payload->transactions) {
			$transaction = $result->payload->transactions[0];
			$transactionState = PayUTransactionState::memberByKey($transaction->transactionResponse->state);
			
			$payment = $paymentDataObject->getPayment();
			$payment->setTransactionId($transaction->id);
			$payment->setPayuTransactionState($transactionState->key());
			
			$payment->setLastTransId($transaction->id);
			$payment->setAdditionalInformation('transactionId', $transaction->id);
			
			$payment->setIsTransactionPending($transactionState->isPendind());
			$payment->setIsTransactionApproved($transactionState->isApproved());
			$payment->setIsTransactionClosed($transactionState->isApproved());
			$payment->setShouldCloseParentTransaction($transactionState->isApproved());
		}
	}
}