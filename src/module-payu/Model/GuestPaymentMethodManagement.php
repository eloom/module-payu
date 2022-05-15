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

namespace Eloom\PayU\Model;

use Eloom\PayU\Api\GuestPaymentMethodManagementInterface;
use Eloom\PayU\Helper\PaymentMethods;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestPaymentMethodManagement implements GuestPaymentMethodManagementInterface {

	/**
	 * @var QuoteIdMaskFactory
	 */
	protected $quoteIdMaskFactory;

	/**
	 * @var PaymentMethods
	 */
	protected $helper;

	/**
	 * GuestPaymentMethodManagement constructor.
	 *
	 * @param QuoteIdMaskFactory $quoteIdMaskFactory
	 */
	public function __construct(QuoteIdMaskFactory $quoteIdMaskFactory, PaymentMethods $helper) {
		$this->quoteIdMaskFactory = $quoteIdMaskFactory;
		$this->helper = $helper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPaymentMethods($cartId, AddressInterface $shippingAddress = null) {
		$quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
		$quoteId = $quoteIdMask->getQuoteId();

		$country = null;
		if ($shippingAddress) {
			$country = $shippingAddress->getCountryId();
		}

		return $this->helper->getPaymentMethods($quoteId, $country);
	}
}
