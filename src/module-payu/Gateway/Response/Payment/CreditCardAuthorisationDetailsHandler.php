<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2022 elOOm (https://eloom.tech)
* @version      2.0.0
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Response\Payment;

use Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class CreditCardAuthorisationDetailsHandler implements HandlerInterface {

	private $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		$transaction = $response[0]['transaction']->transactionResponse;

		$transactionState = PayUTransactionState::memberByKey($transaction->state);

		$payment = $paymentDataObject->getPayment();
		$payment->setTransactionId($transaction->transactionId);
		$payment->setPayuTransactionState($transactionState->key());

		$payment->setLastTransId($transaction->transactionId);
		$payment->setAdditionalInformation('payuOrderId', $transaction->orderId);
		$payment->setAdditionalInformation('transactionId', $transaction->transactionId);

		$payment->setIsTransactionPending($transactionState->isPendind());
		$payment->setIsTransactionClosed($transactionState->isApproved());
		$payment->setShouldCloseParentTransaction($transactionState->isApproved());

		$payment->getOrder()->setCanSendNewEmailFlag(true);
	}
}