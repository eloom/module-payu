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

namespace Eloom\PayU\Services\Payment;

use Eloom\PayU\Connection\Json;
use Eloom\PayU\Resources\Builder\Payment;

class Pricing {

	private $connection;

	public function __construct(Json $connection) {
		$this->connection = $connection;
	}

	public function doPricing($environment, $paymentMethod, $amount, $accountId, $apiKey, $publicKey, $currencyCode) {
		$date = gmdate("D, d M Y H:i:s", time()) . " GMT";
		$contentToSign = utf8_encode('GET' . "\n" . "\n" . "\n" . $date . "\n" . '/payments-api/rest/v4.3/pricing');
		$signature = base64_encode(hash_hmac('sha256', $contentToSign, $apiKey, true));

		$data = ['accountId' => $accountId,
			'currency' => $currencyCode,
			'amount' => $amount,
			'paymentMethod' => $paymentMethod];

		$headers = array('Authorization' => 'Hmac ' . $publicKey . ':' . $signature, 'Date' => $date);

		$url = Payment::getPricingUrl($environment);
		return $this->connection->get($url, $data, $headers);
	}
}