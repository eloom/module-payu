<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.0.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\PayU\Enumeration;

use Eloom\Core\Lib\Enumeration\AbstractMultiton;

class PayUOrderStatus extends AbstractMultiton {

	public function isCaptured() {
		return ($this->key() == 'CAPTURED');
	}

	public function isCancelled() {
		return ($this->key() == 'CANCELLED' || $this->key() == 'DECLINED');
	}

	protected static function initializeMembers() {
		new static('NEW');
		new static('IN_PROGRESS');
		new static('AUTHORIZED');
		new static('CAPTURED');
		new static('CANCELLED');
		new static('DECLINED');
		new static('REFUNDED');
	}

	protected function __construct($key) {
		parent::__construct($key);
	}
}
