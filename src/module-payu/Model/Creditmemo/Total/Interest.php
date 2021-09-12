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

namespace Eloom\PayU\Model\Creditmemo\Total;

class Interest extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
		$creditmemo->setPayuInterestAmount(0);
		$creditmemo->setPayuBaseInterestAmount(0);

		$amount = $creditmemo->getOrder()->getPayuInterestAmount();
		$creditmemo->setPayuInterestAmount($amount);

		$amount = $creditmemo->getOrder()->getPayuBaseInterestAmount();
		$creditmemo->setPayuBaseInterestAmount($amount);

		$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getPayuInterestAmount());
		$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getPayuBaseInterestAmount());

		return $this;
	}
}