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

namespace Eloom\PayU\Plugin;

use Eloom\PayU\Gateway\Config\Cc\Config;
use Magento\Quote\Model\Quote;

class Discount {
	
	private $config;
	
	public function __construct(Config $config) {
		$this->config = $config;
	}
	
	public function getDiscount(Quote $quote, $baseCurrencyCode, $installment) {
		$baseSubtotalWithDiscount = 0;
		$baseTax = 0;
		
		if ($quote->isVirtual()) {
			$address = $quote->getBillingAddress();
		} else {
			$address = $quote->getShippingAddress();
		}
		if ($address) {
			$baseSubtotalWithDiscount = $address->getBaseSubtotalWithDiscount();
			$baseTax = $address->getBaseTaxAmount();
		}
		$discount = $this->config->getCcDiscount();
		
		return \Eloom\PayU\Discount::getInstance($baseCurrencyCode, $discount, $baseSubtotalWithDiscount, $baseTax, $installment);
	}
}