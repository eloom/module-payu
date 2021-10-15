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

namespace Eloom\PayU\Model\Ui\Cc;

use Eloom\PayU\Gateway\Config\Cc\Config as CcConfig;
use Eloom\PayU\Gateway\Config\Config;
use Eloom\PayU\Resources\Builder\Payment;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Payment\Model\CcConfig as PaymentCcConfig;
use Magento\Framework\View\Asset\Repository;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'eloom_payments_payu_cc';

	const CC_VAULT_CODE = 'eloom_payments_payu_cc_vault';

	private $config;

	private $ccConfig;

	private $paymentCcConfig;

	private $session;

	protected $assetRepo;

	public function __construct(SessionManagerInterface $session,
	                            Config $config,
	                            CcConfig $ccConfig,
	                            PaymentCcConfig $paymentCcConfig,
	                            Repository $assetRepo) {
		$this->session = $session;
		$this->config = $config;
		$this->ccConfig = $ccConfig;
		$this->paymentCcConfig = $paymentCcConfig;
		$this->assetRepo = $assetRepo;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();

		$brands = [];
		$showIcon = $this->ccConfig->isShowIcon($storeId);
		if ($showIcon) {
			$iconType = $this->ccConfig->getIconType();
			$iconUri = "Eloom_Payment::images/payment/{$iconType}";
			$brands = [
				'amex' => $this->assetRepo->getUrl("{$iconUri}/amex.svg"),
				'diners' => $this->assetRepo->getUrl("{$iconUri}/diners.svg"),
				'elo' => $this->assetRepo->getUrl("{$iconUri}/elo.svg"),
				'hipercard' => $this->assetRepo->getUrl("{$iconUri}/hipercard.svg"),
				'mastercard' => $this->assetRepo->getUrl("{$iconUri}/mastercard.svg"),
				'visa' => $this->assetRepo->getUrl("{$iconUri}/visa.svg"),
			];
		}

		$payment = [];
		$isActive = $this->ccConfig->isActive($storeId);
		if($isActive) {
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