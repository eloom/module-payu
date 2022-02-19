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

namespace Eloom\PayU\Model\Invoice\Total;

class Interest extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Invoice $invoice
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {
		$invoice->setPayuInterestAmount(0);
		$invoice->setPayuBaseInterestAmount(0);

		$amount = $invoice->getOrder()->getPayuInterestAmount();
		$invoice->setPayuInterestAmount($amount);

		$amount = $invoice->getOrder()->getPayuBaseInterestAmount();
		$invoice->setPayuBaseInterestAmount($amount);

		$invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getPayuInterestAmount());
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getPayuBaseInterestAmount());
		return $this;
	}
}