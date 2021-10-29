<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.3
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Api;

/**
 * Interface for Banks.
 * @api
 * @since 100.0.2
 */
interface BanksOfMexicoInterface {
	
	/**
	 * List all banks.
	 *
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function listBanks();
}