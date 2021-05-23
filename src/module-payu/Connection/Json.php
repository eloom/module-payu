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
			'headers' => array_merge(array('Content-Type' => 'application/json',
				'Accept' => 'application/json'), $headers),
			'query' => $data
		]);

		return $response->getBody()->getContents();
	}
}