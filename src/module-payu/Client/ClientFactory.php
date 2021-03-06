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

namespace Eloom\PayU\Client;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\ObjectManagerInterface;

class ClientFactory {
	
	protected $objectManager = null;
	
	protected $instanceName = null;
	
	public function __construct(ObjectManagerInterface $objectManager,
	                            $instanceName = Curl::class) {
		$this->objectManager = $objectManager;
		$this->instanceName = $instanceName;
	}
	
	public function create(array $data = []) {
		return $this->objectManager->create($this->instanceName, $data);
	}
}
