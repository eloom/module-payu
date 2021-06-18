<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.0
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Model\Ui;

use Eloom\PayU\Gateway\Config\Config;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Asset\Repository;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'eloom_payments_payu';

	protected $assetRepo;

	private $config;

	private $session;

	public function __construct(Repository $assetRepo,
	                            SessionManagerInterface $session,
	                            Config $config) {
		$this->assetRepo = $assetRepo;
		$this->session = $session;
		$this->config = $config;
	}

	public function getCode() {
		return self::CODE;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();
		$currency = $this->config->getStoreCurrency($storeId);

		return [
			'payment' => [
				self::CODE => [
					'language' => Country::memberByKey($currency)->getLanguage(),
					'url' => [
						'logo' => $this->assetRepo->getUrl('Eloom_PayU::images/logo.png')
					]
				]
			]
		];
	}
}