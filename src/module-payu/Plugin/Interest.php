<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.0.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Plugin;

use Eloom\PayU\Gateway\Config\Cc\Config;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;

class Interest {
	
	private $session;
	
	private $ccConfig;
	
	public function __construct(SessionManagerInterface $session, Config $config) {
		$this->session = $session;
		$this->ccConfig = $config;
	}
	
	public function getInterest(Quote $quote, $baseCurrencyCode, $installment) {
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
		
		$interest = $this->getInterestByInstallment($installment);
		$shippingAmount = $address->getBaseShippingAmount();
		
		return \Eloom\PayU\Interests::getInstance($baseCurrencyCode, $interest, $baseSubtotalWithDiscount, $baseTax, $installment, $shippingAmount);
	}
	
	private function getInterestByInstallment($selectedInstallment) {
		$storeId = $this->session->getStoreId();
		$installmentRanges = $this->ccConfig->getInstallmentRanges($storeId);
		$pricingFees = [];
		
		foreach ($installmentRanges as $range) {
			foreach (range($range['from'], $range['to']) as $installment) {
				$pricingFees[$installment] = $range['interest'];
			}
		}
		
		return floatval($pricingFees[$selectedInstallment] ?? 0);
	}
}