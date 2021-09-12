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

namespace Eloom\PayU\Model\Invoice\Total;

class Discount extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Invoice $invoice
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {
		$invoice->setPayuDiscountAmount(0);
		$invoice->setPayuBaseDiscountAmount(0);

		$amount = $invoice->getOrder()->getPayuDiscountAmount();
		$invoice->setPayuDiscountAmount($amount);

		$amount = $invoice->getOrder()->getPayuBaseDiscountAmount();
		$invoice->setPayuBaseDiscountAmount($amount);

		$invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getPayuDiscountAmount());
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getPayuBaseDiscountAmount());

		return $this;
	}
}