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
