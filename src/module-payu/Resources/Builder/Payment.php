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

namespace Eloom\PayU\Resources\Builder;

use Eloom\PayU\Resources\Builder;

class Payment {

	public static function getPaymentsUrl($environment): string {
		return Builder::getService(Builder::getUrl('api', $environment), 'payments');
	}

	public static function getReportsUrl($environment): string {
		return Builder::getService(Builder::getUrl('api', $environment), 'reports');
	}

	public static function getPricingUrl($environment): string {
		return Builder::getService(Builder::getUrl('api', $environment), 'pricing');
	}
}