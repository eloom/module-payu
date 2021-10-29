<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.3
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