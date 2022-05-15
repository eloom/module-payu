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

use Magento\InstantPurchase\PaymentMethodIntegration\AvailabilityCheckerInterface;

class AvailabilityChecker implements AvailabilityCheckerInterface {

	/**
	 * @inheritdoc
	 */
	public function isAvailable(): bool {
		return true;
	}
}
