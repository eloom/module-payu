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

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {
	
	const CODE = 'eloom_discount';
	
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
		
		if ($quote->getNeedApplyEloomPayUDiscount()) {
			$paymentDiscount = $quote->getEloomPayUDiscount();
			$baseTotalDiscountAmount = (($paymentDiscount->baseSubtotalWithDiscount + $paymentDiscount->baseTax) * $paymentDiscount->totalPercent) / 100;
			$baseTotalDiscountAmount = round($baseTotalDiscountAmount, 2);
			$totalDiscountAmount = $this->directoryData->currencyConvert($baseTotalDiscountAmount, $paymentDiscount->baseCurrencyCode);
			
			$total->setPayuBaseDiscountAmount(-$baseTotalDiscountAmount);
			$total->setPayuDiscountAmount(-$totalDiscountAmount);
			
			$total->setGrandTotal($total->getGrandTotal() + $total->getPayuDiscountAmount());
			$total->setBaseGrandTotal($total->getBaseGrandTotal() + $total->getPayuBaseDiscountAmount());
			
			$total->setPayuBaseInterestAmount(0);
			$total->setPayuInterestAmount(0);
		} else if ($quote->getNeedResetEloomPayUDiscount()) {
			$total->setPayuBaseDiscountAmount(0);
			$total->setPayuDiscountAmount(0);
		} else {
			$invokedTotals = $quote->getPayment()->getAdditionalInformation('invokedTotals');
			$paymentMethod = null;
			if ($quote->getPayment()) {
				$paymentMethod = $quote->getPayment()->getMethod();
			}
			if ((ConfigProvider::CODE != $paymentMethod &&
					ConfigProvider::CC_VAULT_CODE != $paymentMethod) ||
				$invokedTotals != $paymentMethod) {
				$total->setPayuBaseDiscountAmount(0);
				$total->setPayuDiscountAmount(0);
			}
		}
		
		return $this;
	}
	
	public function fetch(Quote $quote,
	                      Total $total) {
		return [
			'code' => $this->getCode(),
			'title' => $this->getLabel(),
			'value' => $total->getPayuDiscountAmount()
		];
	}
	
	public function getLabel() {
		return __('Discount');
	}
	
	protected function clearValues(Total $total) {
		$total->setTotalAmount(OrderPaymentPayUInterface::PAYU_DISCOUNT_AMOUNT, 0);
		$total->setBaseTotalAmount(OrderPaymentPayUInterface::PAYU_BASE_DISCOUNT_AMOUNT, 0);
		
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
}