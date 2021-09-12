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

class Discount extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
		$creditmemo->setPayuDiscountAmount(0);
		$creditmemo->setPayuBaseDiscountAmount(0);

		$amount = $creditmemo->getOrder()->getPayuDiscountAmount();
		$creditmemo->setPayuDiscountAmount($amount);

		$amount = $creditmemo->getOrder()->getPayuBaseDiscountAmount();
		$creditmemo->setPayuBaseDiscountAmount($amount);

		$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getPayuDiscountAmount());
		$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getPayuBaseDiscountAmount());

		return $this;
	}
}