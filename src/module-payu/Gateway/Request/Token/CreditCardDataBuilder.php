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

namespace Eloom\PayU\Gateway\Request\Token;

use Eloom\PayU\Gateway\Config\Cc\Config as CcConfig;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Eloom\PayU\Gateway\PayU\Enumeration\PaymentMethod;
use Eloom\PayU\Gateway\Request\Payment\AuthorizeDataBuilder;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\OrderRepository;

class CreditCardDataBuilder implements BuilderInterface {
	
	const CREDIT_CARD_TOKEN_ID = 'creditCardTokenId';
	
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
		
		$extensionAttributes = $payment->getExtensionAttributes();
		$paymentToken = $extensionAttributes->getVaultPaymentToken();
		if ($paymentToken === null) {
			throw new CommandException(__('The Payment Token is not available to perform the request.'));
		}
		$details = json_decode($paymentToken->getTokenDetails() ?: '{}');
		$order = $payment->getOrder();
		$storeId = $order->getStoreId();
		
		$payment->addData(
			[
				OrderPaymentInterface::CC_TYPE => $details->type,
				OrderPaymentInterface::CC_LAST_4 => substr($details->maskedCC, -4)
			]
		);
		
		$data = [
			self::CREDIT_CARD_TOKEN_ID => $paymentToken->getGatewayToken(),
			self::PAYMENT_METHOD => PaymentMethod::memberByKey($details->type)->getCode(),
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