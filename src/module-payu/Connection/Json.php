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

namespace Eloom\PayU\Connection;

use GuzzleHttp\Client;

class Json {
	
	/**
	 * @param $url
	 * @param $data
	 * @param $headers
	 */
	public function get($url, $data, $headers) {
		$client = new Client();
		$response = $client->get($url, [
			'headers' => array_merge(['Content-Type' => 'application/json', 'Accept' => 'application/json'], $headers),
			'query' => $data
		]);
		
		return $response->getBody()->getContents();
	}
}