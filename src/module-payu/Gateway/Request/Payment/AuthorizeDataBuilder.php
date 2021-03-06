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

use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Eloom\PayU\Gateway\PayU\Enumeration\TransactionType;
use Eloom\PayU\Helper\MappedOrderAttributeDefinition;
use Eloom\PayU\Plugin\Signature;
use Eloom\PayU\Resources\Builder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class AuthorizeDataBuilder implements BuilderInterface {

	const TRANSACTION = 'transaction';

	const PAYMENT_COUNTRY = 'paymentCountry';

	const TYPE = 'type';

	const ORDER = 'order';

	const ACCOUNT_ID = 'accountId';

	const REFERENCE_CODE = 'referenceCode';

	const LANGUAGE = 'language';

	const APPLICATION_ID = 'partnerId';

	const DESCRIPTION = 'description';

	const NOTIFY_URL = 'notifyUrl';

	const SIGNATURE = 'signature';

	const IP_ADDRESS = 'ipAddress';

	const BUYER = 'buyer';

	const FULL_NAME = 'fullName';

	const EMAIL_ADDRESS = 'emailAddress';

	const CONTACT_PHONE = 'contactPhone';

	const DNI_NUMBER = 'dniNumber';

	const DNI_TYPE = 'dniType';

	const CNPJ = 'cnpj';

	const BIRTH_DATE = 'birthdate';

	/**
	 * Address
	 */
	const SHIPPING_ADDRESS = 'shippingAddress';
	const BILLING_ADDRESS = 'billingAddress';

	const STREET_1 = 'street1';
	const STREET_2 = 'street2';
	const CITY = 'city';
	const STATE = 'state';
	const COUNTRY = 'country';
	const POSTALCODE = 'postalCode';
	const PHONE = 'phone';

	/**
	 * Payer
	 */
	const PAYER = 'payer';

	protected $urlBuilder;

	private $orderRepository;

	private $config;

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	public function __construct(ConfigInterface $config,
	                            UrlInterface    $urlBuilder,
	                            OrderRepository $orderRepository,
	                            LoggerInterface $logger) {
		$this->config = $config;
		$this->urlBuilder = $urlBuilder;
		$this->orderRepository = $orderRepository;
		$this->logger = $logger;
	}

	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$order = $paymentDataObject->getPayment()->getOrder();

		$storeId = $order->getStoreId();
		$countryCode = $this->config->getCountryCode($storeId);

		$billingAddress = $paymentDataObject->getOrder()->getBillingAddress();
		$shippingAddress = $paymentDataObject->getOrder()->getShippingAddress();
		if (!$shippingAddress) {
			$shippingAddress = $billingAddress;
		}

		$total = $order->getGrandTotal();
		if ($this->config->isPayerPayInterests($storeId)) {
			if ($order->getPayuInterestAmount()) {
				$total -= $order->getPayuInterestAmount();
			}
		}

		$total = number_format($total, 2, '.', '');
		$country = Country::memberByKey($order->getOrderCurrencyCode());

		$paymentData = ObjectManager::getInstance()->get(\Eloom\Payment\Helper\Data::class);
		$ipAddress = $paymentData->getIp($order->getXForwardedFor(), $order->getRemoteIp());

		$result = [];
		$result[self::TYPE] = TransactionType::AUTHORIZATION_AND_CAPTURE()->key();
		$result[self::PAYMENT_COUNTRY] = $countryCode;
		$result[self::IP_ADDRESS] = $ipAddress;

		$street = $billingAddress->getStreet();
		$streetAddress = array_shift($street);
		$extendedAddress = implode(', ', $street);
		$billingStreet1 = substr($streetAddress, 0, 100);
		$billingStreet2 = substr($extendedAddress, 0, 100);

		$street = $billingAddress->getStreet();
		$streetAddress = array_shift($street);
		$extendedAddress = implode(', ', $street);
		$shippingStreet1 = substr($streetAddress ?: '', 0, 100);
		$shippingStreet2 = substr($extendedAddress ?: '', 0, 100);

		/**
		 * Buyer
		 */
		$name = trim($shippingAddress->getFirstname()) . ' ' . trim($shippingAddress->getLastname());

		$attributeDefinition = ObjectManager::getInstance()->get(MappedOrderAttributeDefinition::class);
		$taxvat = $attributeDefinition->getTaxvat($order);
		$taxvat = preg_replace('/\D/', '', $taxvat);

		$buyer = [
			self::FULL_NAME => substr($name, 0, 150),
			self::EMAIL_ADDRESS => $billingAddress->getEmail(),
			self::CONTACT_PHONE => preg_replace('/\D/', '', $shippingAddress->getTelephone()),
			self::DNI_NUMBER => $taxvat,
			self::SHIPPING_ADDRESS => [
				self::STREET_1 => $shippingStreet1,
				self::STREET_2 => $shippingStreet2,
				self::CITY => $shippingAddress->getCity(),
				self::STATE => $shippingAddress->getRegionCode(),
				self::COUNTRY => $shippingAddress->getCountryId(),
				self::POSTALCODE => $shippingAddress->getPostcode(),
				self::PHONE => preg_replace('/\D/', '', $shippingAddress->getTelephone()),
			]
		];

		/**
		 * Order
		 */
		$result[self::ORDER] = [
			self::ACCOUNT_ID => $this->config->getAccountId($storeId),
			self::REFERENCE_CODE => $order->getIncrementId(),
			self::DESCRIPTION => ($this->config->isInSandbox($storeId) ? 'PPS-' : '') . __("Order %1", $order->getIncrementId()),
			self::LANGUAGE => $country->getLanguage(),
			self::NOTIFY_URL => $this->urlBuilder->getUrl('eloompayu/payment/notification', ['_secure' => true]),
			self::APPLICATION_ID => Builder::getInstance()->getApplicationId(),
			self::SIGNATURE => Signature::buildSignature($this->config->getMerchantId($storeId), $this->config->getApiKey($storeId), $total, $country->getCurrency(), $order->getIncrementId(), Signature::MD5_ALGORITHM),
			self::SHIPPING_ADDRESS => [
				self::STREET_1 => $shippingStreet1,
				self::STREET_2 => $shippingStreet2,
				self::CITY => $shippingAddress->getCity(),
				self::STATE => $shippingAddress->getRegionCode(),
				self::COUNTRY => $shippingAddress->getCountryId(),
				self::POSTALCODE => $shippingAddress->getPostcode(),
				self::PHONE => preg_replace('/\D/', '', $shippingAddress->getTelephone()),
			],
			'additionalValues' => [
				'TX_VALUE' => [
					'value' => $total,
					'currency' => $country->getCurrency()
				]
			]
		];

		/**
		 * Payer
		 */
		$payerFone = $billingAddress->getTelephone();

		$name = trim($billingAddress->getFirstname()) . ' ' . trim($billingAddress->getLastname());
		$payer = [
			self::EMAIL_ADDRESS => $billingAddress->getEmail(),
			self::FULL_NAME => $name,
			//self::BIRTH_DATE => $payerBirthDate,
			self::DNI_NUMBER => $taxvat,
			self::CONTACT_PHONE => preg_replace('/\D/', '', $payerFone),
			self::BILLING_ADDRESS => [
				self::STREET_1 => $billingStreet1,
				self::STREET_2 => $billingStreet2,
				self::CITY => $billingAddress->getCity(),
				self::STATE => $billingAddress->getRegionCode(),
				self::COUNTRY => $billingAddress->getCountryId(),
				self::POSTALCODE => $billingAddress->getPostcode(),
				self::PHONE => preg_replace('/\D/', '', $payerFone),
			],
		];
		if ($country->isColombia($countryCode) || $country->isArgentina($countryCode)) {
			$dniType = $attributeDefinition->getDniType($order);
			if ($country->isArgentina($countryCode)) {
				$buyer[self::DNI_TYPE] = $dniType;
				$payer[self::DNI_TYPE] = $dniType;
			} else {
				if (null != $dniType) {
					$buyer[self::DNI_TYPE] = $dniType;
					$payer[self::DNI_TYPE] = $dniType;
				}
			}
		} else if ($country->isBrazil($countryCode)) {
			$buyer[self::DNI_TYPE] = 'CPF';
			$payer[self::DNI_TYPE] = 'CPF';

			if (strlen($taxvat) == 14) {
				$buyer[self::DNI_TYPE] = 'CNPJ';
				$buyer[self::CNPJ] = $taxvat;
				$payer[self::DNI_TYPE] = 'CNPJ';
				$payer[self::CNPJ] = $taxvat;

				unset($buyer[self::DNI_NUMBER]);
				unset($payer[self::DNI_NUMBER]);
			}
		}

		$result[self::ORDER][self::BUYER] = $buyer;

		$result[self::PAYER] = $payer;

		return [self::TRANSACTION => $result];
	}
}