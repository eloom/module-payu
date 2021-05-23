<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.1.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Model;

use Eloom\PayU\Api\BanksInterface;
use Eloom\PayU\Block\Adminhtml\Config\Source\Banks as BanksList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class Banks implements BanksInterface {
	
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