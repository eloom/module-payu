<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.1.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Http\Client;

use Eloom\PayU\Client\ClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Psr\Log\LoggerInterface;

class Reports implements ClientInterface {

	private $clientFactory;

	private $logger;

	public function __construct(ClientFactory $clientFactory, LoggerInterface $logger) {
		$this->clientFactory = $clientFactory;
		$this->logger = $logger;
	}

	public function placeRequest(TransferInterface $transferObject) {
		$log = [
			'request' => $transferObject->getBody(),
			'requestUri' => $transferObject->getUri()
		];
		$response = null;
		$client = $this->clientFactory->create();
		$client->setHeaders($transferObject->getHeaders());

		try {
			$client->post($transferObject->getUri(), $transferObject->getBody());

			$transactionResponse = json_decode($client->getBody());
			$response = ['transaction' => $transactionResponse];
			$log['response'] = json_encode($response);
		} catch (\Magento\Payment\Gateway\Http\ConverterException $e) {
			throw $e;
		} finally {
			$this->logger->info(var_export($log, true));
		}

		return [$response];
	}
}