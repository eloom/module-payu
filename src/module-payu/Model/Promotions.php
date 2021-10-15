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

namespace Eloom\PayU\Model;

use Eloom\PayU\Api\PromotionsInterface;
use Eloom\PayU\Gateway\Config\Cc\Config as CcConfig;
use Eloom\PayU\Gateway\Config\Config;
use Magento\Checkout\Model\Session;
use Magento\Directory\Helper\Data as DirectoryData;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Promotions implements PromotionsInterface {
	
	private $serializer;
	
	private $config;
	
	private $ccConfig;
	
	private $checkoutSession;
	
	/**
	 * @var PriceCurrencyInterface
	 */
	protected $priceCurrency;
	
	private $directoryData;
	
	public function __construct(Json $serializer = null,
	                            Config $config,
	                            CcConfig $ccConfig,
	                            Session $checkoutSession,
	                            DirectoryData $data,
	                            PriceCurrencyInterface $priceCurrency) {
		
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->config = $config;
		$this->ccConfig = $ccConfig;
		$this->checkoutSession = $checkoutSession;
		$this->directoryData = $data;
		$this->priceCurrency = $priceCurrency;
	}
	
	public function getPricing($shippingAmount) {
		$quote = $this->checkoutSession->getQuote();
		$storeId = $quote->getStoreId();
		$installmentRanges = $this->ccConfig->getInstallmentRanges($storeId);
		$baseCurrencyCode = $quote->getBaseCurrencyCode();
		
		$pricingFees = [];
		
		if (is_array($installmentRanges)) {
			$quote->setTotalsCollectedFlag(false)->collectTotals();
			$grandTotal = $quote->getBaseGrandTotal();
			
			$minInstallment = $this->ccConfig->getCcMinInstallment($storeId);
			$percentualDiscount = $this->ccConfig->getCcDiscount($storeId);
			
			$priceAmortization = ObjectManager::getInstance()->get(\Eloom\PayU\Plugin\Amortization\Price::class);
			
			foreach ($installmentRanges as $range) {
				foreach (range($range['from'], $range['to']) as $installment) {
					$interest = $range['interest'];
					
					$totalAmount = $grandTotal;
					$installmentAmount = $totalAmount;
					
					if ($installment == 1 && $percentualDiscount) {
						$installmentAmount = $grandTotal - ((($grandTotal - $shippingAmount) * $percentualDiscount) / 100);
						$totalAmount = $installmentAmount;
					} else {
						if ($installmentAmount < $minInstallment) {
							continue;
						}
						$installmentAmount = $priceAmortization::calculateInstallment($grandTotal, $interest / 100, $installment);
						$totalAmount = $installmentAmount * $installment;
					}
					
					$installmentAmountConverted = $this->directoryData->currencyConvert($installmentAmount, $baseCurrencyCode);
					$totalAmountConverted = $this->directoryData->currencyConvert($totalAmount, $baseCurrencyCode);
					
					if ($installment == 1 && $percentualDiscount) {
						$installmentLabel = __('%1x %2 = %3 (%4% off)', $installment, $this->priceCurrency->format($installmentAmountConverted, false), $this->priceCurrency->format($totalAmountConverted, false), $percentualDiscount);
					} elseif ($interest) {
						$installmentLabel = __('%1x %2 = %3 (c/ interest of %4% a.m.)', $installment, $this->priceCurrency->format($installmentAmountConverted, false), $this->priceCurrency->format($totalAmountConverted, false), $interest);
					} else {
						$installmentLabel = __('%1x %2 = %3', $installment, $this->priceCurrency->format($installmentAmountConverted, false), $this->priceCurrency->format($totalAmountConverted, false));
					}
					
					$installmentValue = $installment . '-' . $installmentAmount;
					$pricingFees[] = [
						'value' => $installmentValue, 'label' => $installmentLabel
					];
				}
			}
		}
		
		return $this->serializer->serialize(['data' => $pricingFees]);
	}
}