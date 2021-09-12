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

namespace Eloom\PayU\Gateway\PayU\Enumeration;

use Eloom\Core\Lib\Enumeration\AbstractMultiton;

class TransactionType extends AbstractMultiton {

	protected static function initializeMembers() {
		new static('AUTHORIZATION');
		new static('AUTHORIZATION_AND_CAPTURE');
		new static('CAPTURE');
		new static('CANCELLATION');
		new static('VOID');
		new static('REFUND');
	}

	protected function __construct($key) {
		parent::__construct($key);
	}

}