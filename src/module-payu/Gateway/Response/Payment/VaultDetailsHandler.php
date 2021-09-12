<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Response\Payment;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Psr\Log\LoggerInterface;

class VaultDetailsHandler implements HandlerInterface {

	protected $paymentTokenFactory;

	protected $paymentExtensionFactory;

	private $serializer;

	private $logger;

	public function __construct(PaymentTokenFactoryInterface $paymentTokenFactory,
															OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
															Json $serializer = null,
															LoggerInterface $logger) {
		$this->paymentTokenFactory = $paymentTokenFactory;
		$this->paymentExtensionFactory = $paymentExtensionFactory;
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		$payment = $paymentDataObject->getPayment();

		$paymentToken = $this->getVaultPaymentToken($response);
		if (null !== $paymentToken) {
			$extensionAttributes = $this->getExtensionAttributes($payment);
			$extensionAttributes->setVaultPaymentToken($paymentToken);
		}
	}

	/**
	 * Get vault payment token entity
	 *
	 * @param array $response
	 * @return PaymentTokenInterface|null
	 */
	private function getVaultPaymentToken(array $response) {
		$paymentToken = null;

		if (!empty($response[0]['token'])) {
			$creditCardToken = $response[0]['token']->creditCardToken;
			$expirationDate = $creditCardToken->expirationDate;

			try {
				$paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
				$paymentToken->setIsVisible(true);
				$paymentToken->setGatewayToken($creditCardToken->creditCardTokenId);
				$paymentToken->setExpiresAt($this->getExpirationDate($expirationDate));

				$details = [
					'type' => strtolower($creditCardToken->paymentMethod),
					'maskedCC' => substr($creditCardToken->maskedNumber, -4),
					'expirationDate' => $expirationDate
				];

				$paymentToken->setTokenDetails(json_encode($details));
			} catch (\Exception $e) {
				$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
			}
		}

		return $paymentToken;
	}

	/**
	 * Get payment extension attributes
	 * @param InfoInterface $payment
	 * @return OrderPaymentExtensionInterface
	 */
	private function getExtensionAttributes(InfoInterface $payment) {
		$extensionAttributes = $payment->getExtensionAttributes();
		if (null === $extensionAttributes) {
			$extensionAttributes = $this->paymentExtensionFactory->create();
			$payment->setExtensionAttributes($extensionAttributes);
		}

		return $extensionAttributes;
	}

	/**
	 * @param $expirationDate
	 * @return string
	 * @throws \Exception
	 */
	private function getExpirationDate($expirationDate) {
		$expirationDate = explode('/', $expirationDate);
		$month = sprintf('%02d', $expirationDate[1]);

		$expDate = new \DateTime(
			$expirationDate[0]
			. '-'
			. $month
			. '-'
			. '01'
			. ' '
			. '00:00:00',
			new \DateTimeZone('UTC')
		);

		// add one month
		$expDate->add(new \DateInterval('P1M'));
		return $expDate->format('Y-m-d 00:00:00');
	}
}