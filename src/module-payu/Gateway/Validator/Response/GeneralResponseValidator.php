<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Validator\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use \Psr\Log\LoggerInterface;

class GeneralResponseValidator extends AbstractValidator {

	protected $errorCodeProvider;

	protected $logger;

	public function __construct(ResultInterfaceFactory $resultFactory,
															ErrorCodeProvider $errorCodeProvider,
															LoggerInterface $logger) {
		parent::__construct($resultFactory);

		$this->errorCodeProvider = $errorCodeProvider;
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function validate(array $validationSubject) {
		$subjectResponse = SubjectReader::readResponse($validationSubject)[0];

		$transactionResponse = $subjectResponse['transaction'];
		$createTokenResponse = null;
		if (isset($subjectResponse['token'])) {
			$createTokenResponse = $subjectResponse['token'];
		}

		$isValid = true;
		$errorMessages = [];
		$errorCodes = [];

		foreach ($this->getResponseValidators() as $validator) {
			$validationResult = $validator($transactionResponse);

			if (!$validationResult[0]) {
				$isValid = $validationResult[0];
				$errorMessages = array_merge($errorMessages, $validationResult[1]);
			}
		}
		if (!$isValid) {
			$errorCodes = $this->errorCodeProvider->getErrorCodes($transactionResponse);
		}

		return $this->createResult($isValid, $errorMessages, $errorCodes);
	}

	protected function getResponseValidators() {
		return [
			function ($response) {
				return [
					property_exists($response, 'code') && $response->code == 'SUCCESS',
					[$response->error ?? __('Invalid response platform payment.')]
				];
			}
		];
	}
}