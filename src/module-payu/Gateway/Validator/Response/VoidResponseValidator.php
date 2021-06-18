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

namespace Eloom\PayU\Gateway\Validator\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class VoidResponseValidator extends AbstractValidator {

	protected $errorCodeProvider;

	public function __construct(ResultInterfaceFactory $resultFactory,
	                            ErrorCodeProvider $errorCodeProvider) {
		parent::__construct($resultFactory);

		$this->errorCodeProvider = $errorCodeProvider;
	}

	/**
	 * @inheritdoc
	 */
	public function validate(array $validationSubject) {
		$subjectResponse = SubjectReader::readResponse($validationSubject);
		$transactionResponse = $subjectResponse['transaction']?? null;

		$isValid = true;
		$errorMessages = [];
		$errorCodes = [];

		/**
		 * o cancelamento ignora erros do gateway / adquirente
		 */
		if (!$isValid) {
			$errorCodes = $this->errorCodeProvider->getErrorCodes($transactionResponse);
		}

		return $this->createResult($isValid, $errorMessages, $errorCodes);
	}
}