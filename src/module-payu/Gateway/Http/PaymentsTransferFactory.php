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

namespace Eloom\PayU\Gateway\Http;

use Eloom\PayU\Gateway\Config\Config;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Eloom\PayU\Resources\Builder\Payment;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Store\Model\StoreManagerInterface;

class PaymentsTransferFactory implements TransferFactoryInterface {

	private $transferBuilder;

	private $config;

	private $logger;

	protected $storeManager;

	public function __construct(TransferBuilder $transferBuilder,
	                            Config $config,
	                            Logger $logger,
	                            StoreManagerInterface $storeManager) {
		$this->transferBuilder = $transferBuilder;
		$this->config = $config;
		$this->logger = $logger;
		$this->storeManager = $storeManager;
	}

	/**
	 * Builds gateway transfer object
	 *
	 * @param array $request
	 * @return TransferInterface
	 */
	public function create(array $request) {
		$body = json_encode($request, JSON_UNESCAPED_SLASHES);

		$store = $this->storeManager->getStore();
		$currency = $store->getCurrentCurrencyCode();

		$response = $this->transferBuilder
			->setMethod('POST')
			->setHeaders(['Content-Type' => 'application/json; charset=UTF-8',
				'Accept' => 'application/json',
				'Accept-Language' => Country::memberByKey($currency)->getLanguage()])
			->setBody($body)
			->setAuthUsername($this->config->getApiKey())
			->setAuthPassword($this->config->getLoginApi())
			->setUri($this->getUrl())
			->build();
		
		return $response;
	}

	private function getUrl() {
		$environment = $this->config->getEnvironment();
		return Payment::getPaymentsUrl($environment);
	}
}