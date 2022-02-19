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

namespace Eloom\PayU\Model\Ui\Cc;

use Eloom\Core\Lib\Enumeration\Exception\UndefinedMemberException;
use Eloom\PayU\Gateway\Config\Cc\Config as CcConfig;
use Eloom\PayU\Gateway\Config\Config;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Eloom\PayU\Resources\Builder\Payment;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Model\CcConfig as PaymentCcConfig;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'eloom_payments_payu_cc';

	const CC_VAULT_CODE = 'eloom_payments_payu_cc_vault';

	private $config;

	private $ccConfig;

	private $paymentCcConfig;

	protected $assetRepo;

	protected $storeManager;

	public function __construct(Config                $config,
	                            CcConfig              $ccConfig,
	                            PaymentCcConfig       $paymentCcConfig,
	                            Repository            $assetRepo,
	                            StoreManagerInterface $storeManager) {
		$this->config = $config;
		$this->ccConfig = $ccConfig;
		$this->paymentCcConfig = $paymentCcConfig;
		$this->assetRepo = $assetRepo;
		$this->storeManager = $storeManager;
	}

	public function getConfig() {
		$store = $this->storeManager->getStore();
		$storeId = $store->getStoreId();
		$payment = [];
		$isActive = $this->ccConfig->isActive($storeId);
		if($isActive) {
			$currency = $store->getCurrentCurrencyCode();
			$supportedCurrency = true;

			try {
				Country::memberByKey($currency);
			} catch (UndefinedMemberException $e) {
				$supportedCurrency = false;
			}

			if (!$supportedCurrency) {
				return ['payment' => [
					self::CODE => [
						'message' =>  sprintf("Currency %s not supported.", $currency)
					]
				]];
			}

			$showIcon = $this->ccConfig->isShowIcon($storeId);
			$brands = [];
			if ($showIcon) {
				$iconType = $this->ccConfig->getIconType();
				$iconUri = "Eloom_Payment::images/payment/{$iconType}";
				$brands = [
					'amex' => $this->assetRepo->getUrl("{$iconUri}/amex.svg"),
					'dinersclub' => $this->assetRepo->getUrl("{$iconUri}/diners.svg"),
					'elo' => $this->assetRepo->getUrl("{$iconUri}/elo.svg"),
					'hipercard' => $this->assetRepo->getUrl("{$iconUri}/hipercard.svg"),
					'mastercard' => $this->assetRepo->getUrl("{$iconUri}/mastercard.svg"),
					'visa' => $this->assetRepo->getUrl("{$iconUri}/visa.svg"),
				];
			}

			$payment = [
				self::CODE => [
					'isActive' => $isActive,
					'accountId' => $this->config->getAccountId($storeId),
					'publicKey' => $this->config->getPublicKey($storeId),
					'url' => [
						'cvv' => $this->paymentCcConfig->getCvvImageUrl(),
						'payments' => Payment::getPaymentsUrl($this->config->getEnvironment($storeId))
					],
					'icons' => [
						'show' => $showIcon,
						'brands' => $brands
					],
					'ccVaultCode' => self::CC_VAULT_CODE,
					'monthsWithoutInterestActive' => $this->ccConfig->isMonthsWithoutInterestActive($storeId)
				]
			];
		}

		return ['payment' => $payment];
	}
}