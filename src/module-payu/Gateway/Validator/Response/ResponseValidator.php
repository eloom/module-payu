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

use Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class ResponseValidator extends GeneralResponseValidator {

	/**
	 * @return array
	 */
	protected function getResponseValidators() {
		return array_merge(
			parent::getResponseValidators(),
			[
				function ($response) {
					return [
						isset($response->code) &&
						isset($response->transactionResponse) &&
						PayUTransactionState::memberByKey($response->transactionResponse->state)->isClientStatusSuccess(),
						[$response->transactionResponse->paymentNetworkResponseErrorMessage ?? null]
					];
				}
			]
		);
	}
}