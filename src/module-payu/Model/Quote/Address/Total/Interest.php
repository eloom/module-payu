<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Model\Quote\Address\Total;

use Eloom\PayU\Api\Data\OrderPaymentPayUInterface;
use Eloom\PayU\Model\Ui\Cc\ConfigProvider;
use Magento\Directory\Helper\Data as DirectoryData;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

class Interest extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {
	
	const CODE = 'eloom_interest';
	
	private $directoryData;
	
	public function __construct(DirectoryData $data) {
		$this->directoryData = $data;
		
		$this->setCode(self::CODE);
	}
	
	public function collect(Quote $quote,
	                        ShippingAssignmentInterface $shippingAssignment,
	                        Total $total) {
		parent::collect($quote, $shippingAssignment, $total);
		
		$items = $shippingAssignment->getItems();
		if (!count($items)) {
			return $this;
		}
		
		if ($quote->getNeedApplyEloomPayUInterest()) {
			$paymentInterest = $quote->getEloomPayUInterest();
			
			$shippingAmount = $paymentInterest->getShippingAmount();
			$amount = round($paymentInterest->baseSubtotalWithDiscount + $paymentInterest->baseTax + $shippingAmount, 2);
			
			$priceAmortization = ObjectManager::getInstance()->get(\Eloom\PayU\Plugin\Amortization\Price::class);
			$interestRate = $paymentInterest->getTotalPercent() / 100;
			$installmentValue = $priceAmortization::calculateInstallment($amount, $interestRate, $paymentInterest->getInstallment());
			
			$baseTotalInterestAmount = ($installmentValue * $paymentInterest->getInstallment()) - $amount;
			
			$totalInterestAmount = $this->directoryData->currencyConvert($baseTotalInterestAmount, $paymentInterest->baseCurrencyCode);
			
			$total->setPayuBaseInterestAmount($baseTotalInterestAmount);
			$total->setPayuInterestAmount($totalInterestAmount);
			
			$total->setGrandTotal($total->getGrandTotal() + $total->getPayuInterestAmount());
			$total->setBaseGrandTotal($total->getBaseGrandTotal() + $total->getPayuBaseInterestAmount());
			
			$total->setPayuBaseDiscountAmount(0);
			$total->setPayuDiscountAmount(0);
		} else if ($quote->getNeedResetEloomPayUInterest()) {
			$total->setPayuBaseInterestAmount(0);
			$total->setPayuInterestAmount(0);
		} else {
			$invokedTotals = $quote->getPayment()->getAdditionalInformation('invokedTotals');
			
			$paymentMethod = null;
			if ($quote->getPayment()) {
				$paymentMethod = $quote->getPayment()->getMethod();
			}
			if ((ConfigProvider::CODE != $paymentMethod &&
					ConfigProvider::CC_VAULT_CODE != $paymentMethod) ||
				($invokedTotals != $paymentMethod)) {
				$total->setPayuBaseInterestAmount(0);
				$total->setPayuInterestAmount(0);
			}
		}
		
		return $this;
	}
	
	protected function clearValues(Total $total) {
		$total->setTotalAmount(OrderPaymentPayUInterface::PAYU_INTEREST_AMOUNT, 0);
		$total->setBaseTotalAmount(OrderPaymentPayUInterface::PAYU_BASE_INTEREST_AMOUNT, 0);
		
		$total->setTotalAmount('subtotal', 0);
		$total->setBaseTotalAmount('subtotal', 0);
		$total->setTotalAmount('tax', 0);
		$total->setBaseTotalAmount('tax', 0);
		$total->setTotalAmount('discount_tax_compensation', 0);
		$total->setBaseTotalAmount('discount_tax_compensation', 0);
		$total->setTotalAmount('shipping_discount_tax_compensation', 0);
		$total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
		$total->setSubtotalInclTax(0);
		$total->setBaseSubtotalInclTax(0);
	}
	
	public function fetch(Quote $quote,
	                      Total $total) {
		return [
			'code' => $this->getCode(),
			'title' => $this->getLabel(),
			'value' => $total->getPayuInterestAmount()
		];
	}
	
	public function getLabel() {
		return __('Interest');
	}
}