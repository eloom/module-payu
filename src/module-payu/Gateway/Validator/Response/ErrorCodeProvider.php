<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.0.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Validator\Response;

class ErrorCodeProvider {

	private $dynamicErrors = ['INVALID_TRANSACTION'];

	public function getErrorCodes($response): array {
		$result = [];
		if(isset($response->transactionResponse)) {
			$code = $response->transactionResponse->responseCode;
			if(in_array($code, $this->dynamicErrors)) {
				if(null !== $response->transactionResponse->responseMessage) {
					$code = $response->transactionResponse->responseMessage;
				}
			}

			$result[] = $code;
		}

		return $result;
	}
}