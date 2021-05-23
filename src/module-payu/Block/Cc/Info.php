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

namespace Eloom\PayU\Block\Cc;

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
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceCurrency = $objectManager->create('Magento\Framework\Pricing\PriceCurrencyInterface');

		return $priceCurrency->format($installmentAmount, false);
	}
}