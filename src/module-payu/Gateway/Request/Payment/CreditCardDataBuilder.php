<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.4
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Request\Payment;

use Eloom\PayU\Gateway\Config\Cc\Config as CcConfig;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Eloom\PayU\Gateway\PayU\Enumeration\PaymentMethod;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;

class CreditCardDataBuilder implements BuilderInterface {
	
	const CREDIT_CARD = 'creditCard';
	
	const NUMBER = 'number';
	
	const SECURITY_CODE = 'securityCode';
	
	const EXPIRATION_DATE = 'expirationDate';
	
	const NAME = 'name';
	
	const PAYMENT_METHOD = 'paymentMethod';
	
	const COOKIE = 'cookie';
	
	const USER_AGENT = 'userAgent';
	
	const DEVICE_SESSION_ID = 'deviceSessionId';
	
	private $ccConfig;
	
	public function __construct(CcConfig $ccConfig) {
		$this->ccConfig = $ccConfig;
	}
	
	/**
	 * Builds ENV request
	 *
	 * @param array $buildSubject
	 * @return array
	 */
	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$payment = $paymentDataObject->getPayment();
		$order = $payment->getOrder();
		$storeId = $order->getStoreId();
		
		$creditCardHash = $payment->getAdditionalInformation(TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH);
		
		/**
		 * buy with credit card token
		 */
		if (null === $creditCardHash) {
			//return [];
		}
		
		/**
		 * Credit Card
		 */
		$creditCardNumber = preg_replace('/[\-\s]+/', '', $payment->getCcNumber());
		$creditCardCvv = $payment->getCcCid();
		$creditCardExp = $payment->getCcExpYear() . '/' . $payment->getCcExpMonth();
		
		$data = [
			self::CREDIT_CARD => [
				self::NUMBER => $creditCardNumber,
				self::EXPIRATION_DATE => $creditCardExp,
				self::SECURITY_CODE => $creditCardCvv,
				self::NAME => $payment->getCcOwner()
			],
			self::PAYMENT_METHOD => PaymentMethod::memberByKey($payment->getCcType())->getCode(),
			self::COOKIE => $payment->getAdditionalInformation('sessionId'),
			self::USER_AGENT => $payment->getAdditionalInformation('userAgent'),
			self::DEVICE_SESSION_ID => $payment->getAdditionalInformation('deviceSessionId'),
			'extraParameters' => [
				'INSTALLMENTS_NUMBER' => $payment->getAdditionalInformation('installments')
			]
		];
		
		$country = Country::memberByKey($order->getOrderCurrencyCode());
		if ($country->isMexico()) {
			if ($this->ccConfig->isMonthsWithoutInterestActive($storeId)) {
				$data['monthsWithoutInterest'] = [
					'months' => $this->ccConfig->getMonthsWithoutInterest($storeId),
					'bank' => $payment->getAdditionalInformation('ccBank')
				];
			}
		}
		
		return [AuthorizeDataBuilder::TRANSACTION => $data];
	}
}