<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.3
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Model\PaymentManagement\Operations;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class PaymentDetailsOperation {

	public function details(OrderPaymentInterface $payment) {
		$order = $payment->getOrder();

		$arguments = ['payment' => $payment, 'amount' => $order->getGrandTotal()];

		if ($payment instanceof InfoInterface) {
			$arguments['payment'] = $this->paymentDataObjectFactory->create($arguments['payment']);
		}

		$method = $payment->getMethodInstance();
		$method->setStore($order->getStoreId());

		/**
		 * Muda o Magento\Payment\Model\Method\Adapter para permitir executar qualquer comando
		 */
		$reflectionClass = new \ReflectionClass($method);

		$paymentAdapter = $reflectionClass->getMethod('executeCommand');
		$paymentAdapter->setAccessible(true);
		$paymentAdapter->invoke($method, 'details', $arguments);

		return $payment;
	}
}
