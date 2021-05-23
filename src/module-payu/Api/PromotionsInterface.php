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
 * Interface for managing promotions.
 * @api
 * @since 100.0.2
 */
interface PromotionsInterface {

	/**
	 * Get princing.
	 *
	 * @param string $shippingAmount
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getPricing($shippingAmount);
}