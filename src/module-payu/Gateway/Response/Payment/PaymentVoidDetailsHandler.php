<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.1.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Response\Payment;

use Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;

class PaymentVoidDetailsHandler implements HandlerInterface {

	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);

		$payment = $paymentDataObject->getPayment();
		/**
		 * ignora o retorno do gateway / operadora e seta o status de CANCELLED
		 */
		$payment->setPayuTransactionState(PayUTransactionState::CANCELLED()->key());
		$payment->setIsTransactionClosed(true);
		$payment->setShouldCloseParentTransaction(true);
	}
}