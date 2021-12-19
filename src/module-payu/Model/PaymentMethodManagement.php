<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.4
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Model;

use Eloom\PayU\Api\PaymentMethodManagementInterface;
use Eloom\PayU\Helper\PaymentMethods;
use Magento\Quote\Api\Data\AddressInterface;

class PaymentMethodManagement implements PaymentMethodManagementInterface {

	/**
	 * @var PaymentMethods
	 */
	protected $helper;

	/**
	 * PaymentMethodManagement constructor.
	 *
	 * @param PaymentMethods $helper
	 */
	public function __construct(PaymentMethods $helper) {
		$this->helper = $helper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPaymentMethods($cartId, AddressInterface $shippingAddress = null) {
		$country = null;
		if ($shippingAddress) {
			$country = $shippingAddress->getCountryId();
		}

		return $this->helper->getPaymentMethods($cartId, $country);
	}
}
