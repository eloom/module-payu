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

namespace Eloom\PayU\Observer;

use Eloom\PayU\Api\Data\OrderPaymentPayUInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Psr\Log\LoggerInterface;

class CreditCadDataAssignObserver extends AbstractDataAssignObserver {
	
	private $logger;
	
	private $encryptor;
	
	private $cookieManager;
	
	private $httpHeader;
	
	public function __construct(LoggerInterface $logger,
	                            EncryptorInterface $encryptor,
	                            CookieManagerInterface $cookieManager,
	                            Header $httpHeader) {
		$this->logger = $logger;
		$this->encryptor = $encryptor;
		$this->cookieManager = $cookieManager;
		$this->httpHeader = $httpHeader;
	}
	
	public function execute(Observer $observer) {
		$data = $this->readDataArgument($observer);
		
		$requestData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
		if (!is_array($requestData)) {
			return;
		}
		if (!is_object($requestData)) {
			$requestData = new DataObject($requestData ?: []);
		}
		$paymentInfo = $this->readPaymentModelArgument($observer);
		$paymentInfo->setAdditionalInformation('sessionId', $this->cookieManager->getCookie('PHPSESSID'));
		$paymentInfo->setAdditionalInformation('userAgent', $this->httpHeader->getHttpUserAgent());
		
		$ccNumber = preg_replace('/[\-\s]+/', '', $requestData->getCcNumber());
		$paymentInfo->addData(
			[
				OrderPaymentPayUInterface::CC_NUMBER_ENC => $ccNumber,
				OrderPaymentPayUInterface::CC_CID_ENC => $requestData->getCcCvv(),
				OrderPaymentInterface::CC_TYPE => $requestData->getCcType(),
				OrderPaymentInterface::CC_OWNER => $requestData->getCcOwner(),
				OrderPaymentInterface::CC_LAST_4 => substr($ccNumber, -4)
			]
		);
		if ($requestData->getCcExpiry() && $requestData->getCcExpiry() != '') {
			$expiry = explode("/", trim($requestData->getCcExpiry()));
			$month = trim($expiry[0]);
			$year = trim($expiry[1]);
			if (strlen($year) == 2) {
				$year = '20' . $year;
			}
			$paymentInfo->addData([
				OrderPaymentInterface::CC_EXP_MONTH => $month,
				OrderPaymentInterface::CC_EXP_YEAR => $year
			]);
		}
		
		if ($requestData->getCcInstallments()) {
			$arrayex = explode('-', $requestData->getCcInstallments());
			if (isset($arrayex[0])) {
				$paymentInfo->setAdditionalInformation('installments', intval($arrayex[0]));
				$paymentInfo->setAdditionalInformation('installmentAmount', floatval($arrayex[1]));
			}
		}
		$paymentInfo->setAdditionalInformation('ccBank', $requestData->getCcBank());
		$paymentInfo->setAdditionalInformation('activePaymentToken', false);
		if ($requestData->getIsActivePaymentTokenEnabler()) {
			$paymentInfo->setAdditionalInformation('activePaymentToken', true);
		}
	}
}