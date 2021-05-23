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

namespace Eloom\PayU\Block\Adminhtml\Config\Source;

class Interests implements \Magento\Framework\Option\ArrayInterface {
	
	public function toOptionArray() {
		return [
			['value' => 'N', 'label' => __('Without interest')],
			['value' => 'P', 'label' => __('Payer pays interest')],
			['value' => 'M', 'label' => __('Merchant pays interest')]
		];
	}
}