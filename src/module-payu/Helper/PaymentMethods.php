<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.5
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Helper;

use Eloom\PayU\Gateway\PayU\Enumeration\Country;
use Eloom\PayU\Model\Ui\Cc\ConfigProvider as CcConfigProvider;
use Eloom\PayU\Model\Ui\ConfigProvider as ConfigProvider;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartRepositoryInterface;

class PaymentMethods extends \Magento\Framework\App\Helper\AbstractHelper {

	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	private $quoteRepository;

	/**
	 * @var ConfigProvider
	 */
	protected $configProvider;

	/**
	 * @var CcConfigProvider
	 */
	protected $ccConfigProvider;

	private $serializer;

	public function __construct(Json                    $serializer = null,
	                            CartRepositoryInterface $quoteRepository,
	                            ConfigProvider          $configProvider,
	                            CcConfigProvider        $ccConfigProvider,
	                            Context                 $context) {
		parent::__construct($context);

		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->quoteRepository = $quoteRepository;
		$this->configProvider = $configProvider;
		$this->ccConfigProvider = $ccConfigProvider;
	}

	/**
	 * @param $quoteId
	 * @param null $country
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getPaymentMethods($quoteId, $country = null) {
		$quote = $this->quoteRepository->getActive($quoteId);

		if (empty($quote)) {
			return [];
		}

		$payment = [
			$this->configProvider->getConfig()['payment'],
			$this->ccConfigProvider->getConfig()['payment']
		];

		$country = Country::memberByWithDefault('getCode', $country);
		if ($country->isArgentina()) {
			try {
				$config = ObjectManager::getInstance()->get(\Eloom\PayUAr\Model\Ui\PagoFacil\ConfigProvider::class);
				$payment[] = $config->getConfig()['payment'];
			} catch (\Exception $e) {
			}
		} elseif ($country->isBrazil()) {
			try {
				$config = ObjectManager::getInstance()->get(\Eloom\PayUBr\Model\Ui\Boleto\ConfigProvider::class);
				$payment[] = $config->getConfig()['payment'];
			} catch (\Exception $e) {
			}
		}

		return $this->serializer->serialize(['payment' => $payment]);
	}
}