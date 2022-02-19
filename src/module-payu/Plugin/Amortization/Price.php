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

namespace Eloom\PayU\Plugin\Amortization;

class Price {
	
	/**
	 * Price { R = P x [ i (1 + i)n ] ÷ [ (1 + i )n -1] }
	 * @param $total
	 * @param $interestRate
	 * @param $numberOfPayments
	 * @return float
	 */
	public static function calculateInstallment($total, $interestRate, $numberOfPayments) {
		$price = 0;
		if ($interestRate != 0) {
			$price = $total * (($interestRate * pow((1 + $interestRate), $numberOfPayments)) / (pow((1 + $interestRate), $numberOfPayments) - 1));
		} else {
			$price = $total / $numberOfPayments;
		}
		
		return round($price, 2);
	}
}