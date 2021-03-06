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

namespace Eloom\PayU\Api;

/**
 * Interface for Totals.
 * @api
 * @since 100.0.2
 */
interface TotalsInterface {
	
	/**
	 * Reload totals.
	 *
	 * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
	 * @param string $shippingAmount
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function reload(\Magento\Quote\Api\Data\PaymentInterface $paymentMethod, $shippingAmount);
}