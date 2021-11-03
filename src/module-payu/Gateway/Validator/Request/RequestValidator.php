<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.3
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Validator\Request;

use Eloom\Core\Exception\TaxvatException;
use Eloom\Core\Model\ResourceModel\Taxvat\ValidatorHandlerFactory;
use Eloom\PayU\Helper\MappedQuoteAttributeDefinition;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Psr\Log\LoggerInterface;

class RequestValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator {

	protected $checkoutSession;

	protected $validatorHandlerFactory;

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	public function __construct(ResultInterfaceFactory  $resultFactory,
	                            Session                 $checkoutSession,
	                            ValidatorHandlerFactory $validatorHandlerFactory,
	                            LoggerInterface         $logger) {
		$this->checkoutSession = $checkoutSession;
		$this->validatorHandlerFactory = $validatorHandlerFactory;
		$this->logger = $logger;

		parent::__construct($resultFactory);
	}

	public function validate(array $validationSubject): ResultInterface {
		$isValid = true;
		$fails = array();

		$quote = $this->checkoutSession->getQuote();
		try {
			$attributeDefinition = ObjectManager::getInstance()->get(MappedQuoteAttributeDefinition::class);
			$taxvat = $attributeDefinition->getTaxvat($quote);;
			$factory = $this->validatorHandlerFactory->create();
			$result = $factory->validate($taxvat);
		} catch (TaxvatException $ex) {
			$this->logger->critical($ex->getMessage());
			$isValid = false;
			array_push($fails, __($ex->getMessage()));
		}

		return $this->createResult($isValid, $fails);
	}
}