<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Response\OrderDetails;

use Eloom\PayU\Gateway\PayU\Enumeration\PayUOrderStatus;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class DetailsHandler implements HandlerInterface {
	
	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		$result = $response[0]['transaction']->result;
		$payment = $paymentDataObject->getPayment();
		
		if ($result->payload->transactions) {
			$transaction = $result->payload->transactions[0];
			$payment->setPayuOrderStatus(PayUOrderStatus::memberByKey($result->payload->status));// transient method
			
			if ($payment->getPayuOrderStatus()->isCancelled()) {
				$payment->setIsTransactionClosed(true);
				$payment->setShouldCloseParentTransaction(true);
			}
			
			$payment->setLastTransId($transaction->id);
			$payment->setTransactionId($transaction->id);
			if (isset($transaction->parentTransactionId)) {
				$payment->setParentTransactionId($transaction->parentTransactionId);
				$payment->setTransactionId($transaction->parentTransactionId);
				
				$payment->setAdditionalInformation('transactionId', $transaction->id);
			}
			
			$payment->setPayuTransactionState($transaction->transactionResponse->state);
		}
	}
}