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

namespace Eloom\PayU\Client;

class ClientFactory {

	protected $objectManager = null;

	protected $instanceName = null;

	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = \Magento\Framework\HTTP\Client\Curl::class) {
		$this->objectManager = $objectManager;
		$this->instanceName = $instanceName;
	}

	public function create(array $data = []) {
		return $this->objectManager->create($this->instanceName, $data);
	}
}
