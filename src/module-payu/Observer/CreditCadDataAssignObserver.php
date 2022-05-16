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
		
		$additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
		if (!is_array($additionalData)) {
			return;
		}
		if (!is_object($additionalData)) {
			$additionalData = new DataObject($additionalData ?: []);
		}
		$paymentInfo = $this->readPaymentModelArgument($observer);
		$paymentInfo->setAdditionalInformation('sessionId', $this->cookieManager->getCookie('PHPSESSID'));
		$paymentInfo->setAdditionalInformation('userAgent', $this->httpHeader->getHttpUserAgent());
		$paymentInfo->setAdditionalInformation('deviceSessionId', $additionalData->getCcDeviceSessionId());

		$ccNumber = preg_replace('/[\-\s]+/', '', $additionalData->getCcNumber()?? '');
		$paymentInfo->addData(
			[
				OrderPaymentPayUInterface::CC_NUMBER_ENC => $ccNumber,
				OrderPaymentPayUInterface::CC_CID_ENC => $additionalData->getCcCvv(),
				OrderPaymentInterface::CC_TYPE => $additionalData->getCcType(),
				OrderPaymentInterface::CC_OWNER => $additionalData->getCcOwner(),
				OrderPaymentInterface::CC_LAST_4 => substr($ccNumber, -4)
			]
		);
		if ($additionalData->getCcExpiry() && $additionalData->getCcExpiry() != '') {
			$expiry = explode("/", trim($additionalData->getCcExpiry()));
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

		if ($additionalData->getCcInstallments()) {
			$arrayex = explode('-', $additionalData->getCcInstallments());
			if (isset($arrayex[0])) {
				$paymentInfo->setAdditionalInformation('installments', intval($arrayex[0]));
				$paymentInfo->setAdditionalInformation('installmentAmount', floatval($arrayex[1]));
			}
		} else {
			$paymentInfo->setAdditionalInformation('installments', intval(1));
			$paymentInfo->setAdditionalInformation('installmentAmount', floatval($additionalData->getGrandTotal()));
		}
		$paymentInfo->setAdditionalInformation('ccBank', $additionalData->getCcBank());
		$paymentInfo->setAdditionalInformation('activePaymentToken', false);
		if ($additionalData->getIsActivePaymentTokenEnabler()) {
			$paymentInfo->setAdditionalInformation('activePaymentToken', true);
		}
	}
}