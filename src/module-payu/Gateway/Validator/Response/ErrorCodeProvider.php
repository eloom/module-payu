<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2022 elOOm (https://eloom.tech)
* @version      2.0.0
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Validator\Response;

class ErrorCodeProvider {

	private $dynamicErrors = ['ERROR','INVALID_TRANSACTION'];

	public function getErrorCodes($response): array {
		$result = [];
		if(isset($response->transactionResponse)) {
			$code = $response->transactionResponse->responseCode;

			if(in_array($code, $this->dynamicErrors)) {
				if(null !== $response->transactionResponse->responseMessage) {
					$code = $response->transactionResponse->responseMessage;
				}
				if(null !== $response->transactionResponse->paymentNetworkResponseErrorMessage) {
					$code = $response->transactionResponse->paymentNetworkResponseErrorMessage;
				}
			}
			$result[] = $code;
		}

		return $result;
	}
}