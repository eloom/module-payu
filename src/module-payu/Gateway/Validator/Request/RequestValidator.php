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

namespace Eloom\PayU\Gateway\Validator\Request;

use Eloom\Core\Exception\TaxvatException;
use Eloom\Core\Model\ResourceModel\Taxvat\ValidatorHandlerFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class RequestValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator {
	
	protected $checkoutSession;
	
	protected $validatorHandlerFactory;
	
	public function __construct(ResultInterfaceFactory $resultFactory,
	                            Session $checkoutSession,
	                            ValidatorHandlerFactory $validatorHandlerFactory) {
		$this->checkoutSession = $checkoutSession;
		$this->validatorHandlerFactory = $validatorHandlerFactory;
		
		parent::__construct($resultFactory);
	}
	
	public function validate(array $validationSubject): ResultInterface {
		$isValid = true;
		$fails = array();
		
		$quote = $this->checkoutSession->getQuote();
		
		try {
			$taxvat = ($quote->getCustomerTaxvat() ? $quote->getCustomerTaxvat() : $quote->getBillingAddress()->getVatId());
			$factory = $this->validatorHandlerFactory->create();
			$result = $factory->validate($taxvat);
		} catch (TaxvatException $ex) {
			$isValid = false;
			array_push($fails, __($ex->getMessage()));
		}
		
		return $this->createResult($isValid, $fails);
	}
}