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
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;
use Psr\Log\LoggerInterface;

class CcRequestValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator {

	protected $checkoutSession;

	protected $validatorHandlerFactory;

	private $logger;

	public function __construct(ResultInterfaceFactory  $resultFactory,
	                            Session                 $checkoutSession,
	                            LoggerInterface         $logger,
	                            ValidatorHandlerFactory $validatorHandlerFactory) {
		$this->checkoutSession = $checkoutSession;
		$this->logger = $logger;
		$this->validatorHandlerFactory = $validatorHandlerFactory;

		parent::__construct($resultFactory);
	}

	public function validate(array $validationSubject): ResultInterface {
		$isValid = true;
		$fails = array();

		$quote = $this->checkoutSession->getQuote();

		try {
			$attributeDefinition = ObjectManager::getInstance()->get(MappedQuoteAttributeDefinition::class);
			$taxvat = $attributeDefinition->getTaxvat($quote);
			$factory = $this->validatorHandlerFactory->create();
			$result = $factory->validate($taxvat);
		} catch (TaxvatException $ex) {
			$this->logger->critical($ex->getMessage());
			$isValid = false;
			array_push($fails, __($ex->getMessage()));
		}

		$payment = $validationSubject['payment'];
		$creditCardHash = $payment->getAdditionalInformation(TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH);

		/**
		 * buy with credit card token
		 */
		if (null === $creditCardHash) {
			return $this->createResult($isValid, $fails);
		}

		$ccNumber = $payment->getCcNumber();
		$card = new \Eloom\Payment\Lib\CreditCard\CardNumber();
		if (!$card->passes(intval($ccNumber))) {
			$isValid = false;
			array_push($fails, $card->message());
		}

		$cvvNumber = $payment->getCcCid();
		$cvv = new \Eloom\Payment\Lib\CreditCard\CardCvc($ccNumber);
		if (!$cvv->passes(intval($cvvNumber))) {
			$isValid = false;
			array_push($fails, $cvv->message());
		}

		$date = new \Eloom\Payment\Lib\CreditCard\CardExpirationDate();
		$creditCardExpiry = $payment->getCcExpYear() . '/' . $payment->getCcExpMonth();
		if (!$date->passes($creditCardExpiry)) {
			$isValid = false;
			array_push($fails, $date->message());
		}

		return $this->createResult($isValid, $fails);
	}
}