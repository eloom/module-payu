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

namespace Eloom\PayU\Api;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Interface GuestPaymentMethodManagementInterface
 *
 * @api
 */
interface PaymentMethodManagementInterface {

	/**
	 * Get payment information
	 *
	 * @param string $cartId
	 * @param null|AddressInterface
	 * @return string
	 */
	public function getPaymentMethods($cartId, AddressInterface $shippingAddress = null);
}
