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

namespace Eloom\PayU\Model\InstantPurchase\CreditCard;

use Magento\InstantPurchase\PaymentMethodIntegration\PaymentTokenFormatterInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;

class TokenFormatter implements PaymentTokenFormatterInterface {

	/**
	 * Most used credit card types
	 * @var array
	 */
	public static $baseCardTypes = [
		'amex' => 'American Express',
		'visa' => 'Visa',
		'mastercard' => 'MasterCard',
		'elo' => 'Elo',
		'dinersclub' => 'Diners'
	];

	/**
	 * @inheritdoc
	 */
	public function formatPaymentToken(PaymentTokenInterface $paymentToken): string {
		$details = json_decode($paymentToken->getTokenDetails() ?: '{}', true);
		if (!isset($details['type'], $details['maskedCC'], $details['expirationDate'])) {
			throw new \InvalidArgumentException('Invalid PayU credit card token details.');
		}

		if (isset(self::$baseCardTypes[$details['type']])) {
			$ccType = self::$baseCardTypes[$details['type']];
		} else {
			$ccType = $details['type'];
		}

		$formatted = sprintf(
			'%s: %s, %s: %s (%s: %s)',
			__('Credit Card'),
			$ccType,
			__('ending'),
			$details['maskedCC'],
			__('expires'),
			$details['expirationDate']
		);

		return $formatted;
	}
}
