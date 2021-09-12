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

namespace Eloom\PayU\Gateway\Config\Cc;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Unserialize\Unserialize;

class Config extends \Magento\Payment\Gateway\Config\Config {
	
	const KEY_ACTIVE = 'active';
	
	const ICON = 'icon';
	
	const KEY_CC_DISCOUNT = 'discount';
	
	const KEY_CC_MIN_INSTALLMENT = 'min_installment';
	
	const INSTALLMENT_RANGES = 'installment_ranges';
	
	const MONTHS_WITHOUT_INTEREST_ACTIVE = 'months_without_interest_active';
	
	const MONTHS_WITHOUT_INTEREST = 'months_without_interest';
	
	private $serializer;
	
	public function __construct(ScopeConfigInterface $scopeConfig,
	                            $methodCode = null,
	                            $pathPattern = self::DEFAULT_PATH_PATTERN,
	                            Json $serializer = null) {
		parent::__construct($scopeConfig, $methodCode, $pathPattern);
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
	}
	
	public function isActive($storeId = null) {
		return (bool)$this->getValue(self::KEY_ACTIVE, $storeId);
	}
	
	public function isShowIcon($storeId = null) {
		return (bool)($this->getIconType($storeId) != 'none');
	}
	
	public function getIconType($storeId = null) {
		return $this->getValue(self::ICON, $storeId);
	}
	
	public function getCcDiscount($storeId = null) {
		$v = $this->getValue(self::KEY_CC_DISCOUNT, $storeId);
		if ($v) {
			return str_replace(',', '.', $v);
		}
		
		return 0.00;
	}
	
	public function getCcMinInstallment($storeId = null) {
		$v = $this->getValue(self::KEY_CC_MIN_INSTALLMENT, $storeId);
		if ($v) {
			return str_replace(',', '.', $v);
		}
		
		return 0.00;
	}
	
	public function getInstallmentRanges($storeId = null) {
		$value = $this->getValue(self::INSTALLMENT_RANGES, $storeId);
		
		if (empty($value)) return false;
		
		if ($this->isSerialized($value)) {
			$unserializer = ObjectManager::getInstance()->get(Unserialize::class);
		} else {
			$unserializer = ObjectManager::getInstance()->get(Json::class);
		}
		
		return $unserializer->unserialize($value);
	}
	
	/**
	 * Check if value is a serialized string
	 *
	 * @param string $value
	 * @return boolean
	 */
	private function isSerialized($value) {
		return (boolean)preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
	}
	
	public function isMonthsWithoutInterestActive($storeId = null) {
		return (bool)$this->getValue(self::MONTHS_WITHOUT_INTEREST_ACTIVE, $storeId);
	}
	
	public function getMonthsWithoutInterest($storeId = null) {
		return (int)$this->getValue(self::MONTHS_WITHOUT_INTEREST, $storeId);
	}
}