<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.2
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Model;

use Eloom\PayU\Api\BanksOfMexicoInterface;
use Eloom\PayU\Block\Adminhtml\Config\Source\Banks as BanksList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class BanksOfMexico implements BanksOfMexicoInterface {
	
	private $serializer;
	
	private $banksList;
	
	public function __construct(Json $serializer = null,
	                            BanksList $banksList) {
		
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->banksList = $banksList;
	}
	
	public function listBanks() {
		$options = $this->banksList->toOptionArray();
		$banks = [];
		
		foreach ($options as $option) {
			$banks[] = [
				'value' => $option['value'], 'label' => $option['label']
			];
		}
		
		return $this->serializer->serialize(['data' => $banks]);
	}
}