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

class Banks implements \Magento\Framework\Option\ArrayInterface {
	
	public function toOptionArray() {
		return [
			['value' => 'AFIRME', 'label' => __('Afirme')],
			['value' => 'BANREGIO', 'label' => __('Banregio')],
			['value' => 'BANAMEX', 'label' => __('Banamex')],
			['value' => 'BANCO REGIONAL DE MONTERREY S.A', 'label' => __('Banco Regional de Monterrey S.A')],
			['value' => 'BANCOPPEL', 'label' => __('Bancoppel')],
			['value' => 'BANCOMER', 'label' => __('Bancomer')],
			['value' => 'BANCA MIFEL SA', 'label' => __('Banca Mifel S.A')],
			['value' => 'BANCO MULTIVA', 'label' => __('Banco Multiva')],
			['value' => 'BANCO AZTECA', 'label' => __('Banco Azteca')],
			['value' => 'BAJIO', 'label' => __('Bajio')],
			['value' => 'BANJERCITO', 'label' => __('Banjercito')],
			['value' => 'BANORTE', 'label' => __('Banorte')],
			['value' => 'CI BANCO', 'label' => __('CI Banco')],
			['value' => 'FAMSA', 'label' => __('Famsa')],
			['value' => 'HSBC', 'label' => __('HSBC')],
			['value' => 'INBURSA', 'label' => __('Inbursa')],
			['value' => 'INVEX', 'label' => __('Invex')],
			['value' => 'PREMIUM CARD LIVERPOOL', 'label' => __('Premium Card Liverpool')],
			['value' => 'SANTANDER', 'label' => __('Santander')],
			['value' => 'SCOTIABANK', 'label' => __('ScotiaBank')],
		];
	}
}