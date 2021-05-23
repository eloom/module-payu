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