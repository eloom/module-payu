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

class Environment implements \Magento\Framework\Option\ArrayInterface {

	const PRODUCTION = 'production';
	const SANDBOX = 'sandbox';
	/**
	 * {@inheritdoc}
	 */
	public function toOptionArray() {
		return [
			['value' => self::PRODUCTION, 'label' => __('Production')],
			['value' => self::SANDBOX, 'label' => __('Sandbox')]
		];
	}
}