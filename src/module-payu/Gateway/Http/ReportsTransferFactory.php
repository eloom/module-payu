<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Http;

use Eloom\PayU\Gateway\Config\Config;
use Eloom\PayU\Resources\Builder\Payment;
use GuzzleHttp\Client;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Psr\Log\LoggerInterface;

class ReportsTransferFactory implements TransferFactoryInterface {

	private $transferBuilder;

	private $config;

	private $logger;

	public function __construct(TransferBuilder $transferBuilder, Config $config, LoggerInterface $logger) {
		$this->transferBuilder = $transferBuilder;
		$this->config = $config;
		$this->logger = $logger;
	}

	/**
	 * Builds gateway transfer object
	 *
	 * @param array $request
	 * @return TransferInterface
	 */
	public function create(array $request) {
		$body = json_encode($request, JSON_UNESCAPED_SLASHES);
		
		$response = $this->transferBuilder
			->setMethod('POST')
			->setHeaders(['Content-Type' => 'application/json; charset=UTF-8',
				'Accept' => 'application/json',
				'Accept-Language' => $request['language']])
			->setBody($body)
			->setAuthUsername($request['merchant']['apiKey'])
			->setAuthPassword($request['merchant']['apiLogin'])
			->setUri($this->getUrl())
			->build();

		return $response;
	}

	private function getUrl() {
		$environment = $this->config->getEnvironment();
		return Payment::getReportsUrl($environment);
	}
}