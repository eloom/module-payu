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

namespace Eloom\PayU\Gateway\Request\Payment;

use Eloom\PayU\Gateway\PayU\CommandInterface;
use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class GeneralDataBuilder implements BuilderInterface {

	const LANGUAGE = 'language';

	const COMMAND = 'command';

	const TEST = 'test';

	private $config;

	public function __construct(ConfigInterface $config) {
		$this->config = $config;
	}

	/**
	 * Builds ENV request
	 *
	 * @param array $buildSubject
	 * @return array
	 */
	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$order = $paymentDataObject->getPayment()->getOrder();
		$currency = $order->getOrderCurrencyCode();
		$storeId = $order->getStoreId();
		
		return [
			self::LANGUAGE => Country::memberByKey($currency)->getLanguage(),
			self::COMMAND => CommandInterface::PAYMENT_SUBMIT_TRANSACTION,
			self::TEST => $this->config->isTransactionInTestMode($storeId),
			'merchant' => [
				'apiKey' => $this->config->getApiKey($storeId),
				'apiLogin' => $this->config->getLoginApi($storeId)
			]
		];
	}
}