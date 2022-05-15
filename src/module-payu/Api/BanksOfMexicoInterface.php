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