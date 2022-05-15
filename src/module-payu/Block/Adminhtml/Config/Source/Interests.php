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