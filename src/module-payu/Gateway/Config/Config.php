<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\Config;

use Eloom\PayU\Block\Adminhtml\Config\Source\Environment;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Payment\Gateway\Config\Config {

	const KEY_ACTIVE = 'active';

	const KEY_ENVIRONMENT = 'environment';

	/**
	 * Merchant ID
	 */
	const KEY_MERCHANT_ID = 'merchant_id';

	/**
	 * API Key
	 */
	const KEY_API_KEY = 'api_key';

	/**
	 * Account ID
	 */
	const KEY_ACCOUNT_ID = 'account_id';

	/**
	 * Public Key
	 */
	const KEY_PUBLIC_KEY = 'public_key';

	/**
	 * Login API
	 */
	const KEY_LOGIN_API = 'login_api';

	const INTERESTS = 'interests';

	private $scopeConfig;

	private $serializer;

	/**
	 *
	 * @param ScopeConfigInterface $scopeConfig
	 * @param null|string $methodCode
	 * @param string $pathPattern
	 */
	public function __construct(ScopeConfigInterface $scopeConfig,
	                            $methodCode = null,
	                            $pathPattern = self::DEFAULT_PATH_PATTERN,
	                            Json $serializer = null) {
		parent::__construct($scopeConfig, $methodCode, $pathPattern);

		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->scopeConfig = $scopeConfig;
	}

	/**
	 * Gets Payment configuration status.
	 *
	 * @param int|null $storeId
	 * @return bool
	 */
	public function isActive($storeId = null) {
		return (bool)$this->getValue(self::KEY_ACTIVE, $storeId);
	}

	/**
	 * Gets value of configured environment.
	 * Possible values: production or sandbox.
	 *
	 * @param int|null $storeId
	 * @return string
	 */
	public function getEnvironment($storeId = null) {
		return $this->getValue(self::KEY_ENVIRONMENT, $storeId);
	}

	public function isInProduction() {
		return (bool)($this->getEnvironment() == Environment::PRODUCTION);
	}

	public function isInSandbox() {
		return (bool)($this->getEnvironment() == Environment::SANDBOX);
	}

	public function getMerchantId($storeId = null): string {
		$v = $this->getValue(self::KEY_MERCHANT_ID, $storeId);
		return (!empty($v) ? trim($v) : '');
	}

	public function getApiKey($storeId = null): string {
		$v = $this->getValue(self::KEY_API_KEY, $storeId);
		return (!empty($v) ? trim($v) : '');
	}

	public function getAccountId($storeId = null): string {
		$v = $this->getValue(self::KEY_ACCOUNT_ID, $storeId);
		return (!empty($v) ? trim($v) : '');
	}

	public function getLoginApi($storeId = null): string {
		$v = $this->getValue(self::KEY_LOGIN_API, $storeId);
		return (!empty($v) ? trim($v) : '');
	}

	public function getPublicKey($storeId = null): string {
		$v = $this->getValue(self::KEY_PUBLIC_KEY, $storeId);
		return (!empty($v) ? trim($v) : '');
	}

	public function getInterests($storeId = null) {
		return $this->getValue(self::INTERESTS, $storeId);
	}

	public function isPayerPayInterests($storeId = null) {
		return ($this->getInterests($storeId) == 'P');
	}

	public function getStoreCurrency($storeId = null) {
		return $this->scopeConfig->getValue('currency/options/default', ScopeInterface::SCOPE_STORE, $storeId);
	}
}