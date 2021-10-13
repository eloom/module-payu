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

namespace Eloom\PayU\Model\Ui;

use Eloom\Core\Lib\Enumeration\Exception\UndefinedMemberException;
use Eloom\PayU\Gateway\Config\Config;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Psr\Log\LoggerInterface;

class ConfigProvider implements ConfigProviderInterface {
	
	const CODE = 'eloom_payments_payu';
	
	protected $assetRepo;
	
	private $config;
	
	private $session;
	
	private $encryptor;
	
	private $cookieManager;
	
	private $logger;
	
	public function __construct(Repository              $assetRepo,
	                            SessionManagerInterface $session,
	                            Config                  $config,
	                            EncryptorInterface      $encryptor,
	                            CookieManagerInterface  $cookieManager,
	                            LoggerInterface         $logger) {
		$this->assetRepo = $assetRepo;
		$this->session = $session;
		$this->config = $config;
		$this->encryptor = $encryptor;
		$this->cookieManager = $cookieManager;
		$this->logger = $logger;
	}
	
	public function getCode() {
		return self::CODE;
	}
	
	public function getConfig() {
		$storeId = $this->session->getStoreId();
		$currency = $this->config->getStoreCurrency($storeId);
		$sessionId = $this->cookieManager->getCookie('PHPSESSID');
		
		$payment = [];
		
		try {
			$payment[self::CODE] = [
				'language' => Country::memberByKey($currency)->getLanguage(),
				'isInSandboxMode' => $this->config->isInSandbox($storeId),
				'isTransactionInTestMode' => $this->config->isTransactionInTestMode($storeId),
				'deviceSessionId' => md5($sessionId . microtime()),
				'url' => [
					'logo' => $this->assetRepo->getUrl('Eloom_PayU::images/logo.png')
				]
			];
		} catch (UndefinedMemberException $e) {
			$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
			
			$payment[self::CODE] = [
				'message' => $e->getMessage()
			];
		}
		
		return ['payment' => $payment];
	}
}