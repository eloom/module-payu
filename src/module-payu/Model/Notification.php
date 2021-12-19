<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.4
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Notification extends AbstractModel {

	protected function _construct() {
		$this->_init('Eloom\PayU\Model\ResourceModel\Notification');
	}
}