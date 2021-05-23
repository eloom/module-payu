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