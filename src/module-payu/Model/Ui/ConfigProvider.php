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

namespace Eloom\PayU\Model\Ui;

use Eloom\Core\Lib\Enumeration\Exception\UndefinedMemberException;
use Eloom\PayU\Gateway\Config\Config;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ConfigProvider implements ConfigProviderInterface {
	
	const CODE = 'eloom_payments_payu';
	
	protected $assetRepo;
	
	private $config;
	
	private $encryptor;
	
	private $cookieManager;
	
	private $logger;

	protected $storeManager;

	public function __construct(Repository              $assetRepo,
	                            Config                  $config,
	                            EncryptorInterface      $encryptor,
	                            CookieManagerInterface  $cookieManager,
	                            LoggerInterface         $logger,
	                            StoreManagerInterface $storeManager) {
		$this->assetRepo = $assetRepo;
		$this->config = $config;
		$this->encryptor = $encryptor;
		$this->cookieManager = $cookieManager;
		$this->logger = $logger;
		$this->storeManager = $storeManager;
	}
	
	public function getCode() {
		return self::CODE;
	}
	
	public function getConfig() {
		$store = $this->storeManager->getStore();
		$storeId = $store->getStoreId();
		$currency = $store->getCurrentCurrencyCode();
		$sessionId = $this->cookieManager->getCookie('PHPSESSID');

		$payment = [];
		
		try {
			$payment[self::CODE] = [
				'language' => Country::memberByKey($currency)->getLanguage(),
				'isInSandboxMode' => $this->config->isInSandbox($storeId),
				'isTransactionInTestMode' => $this->config->isTransactionInTestMode($storeId),
				'deviceSessionId' => md5($sessionId . microtime()),
				'url' => [
					'logo' => $this->assetRepo->getUrl('Eloom_PayU::images/payu.svg')
				]
			];
		} catch (UndefinedMemberException $e) {
			$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
			
			$payment[self::CODE] = [
				'message' => sprintf("Currency %s not supported.", $currency)
			];
		}
		
		return ['payment' => $payment];
	}
}