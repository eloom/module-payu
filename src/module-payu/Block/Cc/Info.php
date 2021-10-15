<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.2
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Block\Cc;

use Magento\Framework\App\ObjectManager;

class Info extends \Eloom\PayU\Block\Info {
	
	public function getCcType() {
		return $this->getInfo()->getCcType();
	}
	
	public function getCcLast4() {
		if ($this->getInfo()->getCcLast4()) {
			return sprintf('xxxx xxxx xxxx %s', $this->getInfo()->getCcLast4());
		}
		
		return null;
	}
	
	public function getInstallments() {
		$installments = $this->getInfo()->getAdditionalInformation('installments');
		if (!empty($installments)) {
			return __("In %1x of %2", $installments, $this->getFormattedInstallmentAmount($this->getInfo()->getAdditionalInformation('installmentAmount')));
		}
		
		return null;
	}
	
	private function getFormattedInstallmentAmount($installmentAmount) {
		$objectManager = ObjectManager::getInstance();
		$priceCurrency = $objectManager->create('Magento\Framework\Pricing\PriceCurrencyInterface');
		
		return $priceCurrency->format($installmentAmount, false);
	}
}