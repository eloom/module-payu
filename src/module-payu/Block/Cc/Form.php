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

namespace Eloom\PayU\Block\Cc;

use Eloom\PayU\Gateway\Config\Cc\Config;
use Magento\Payment\Helper\Data;

class Form extends \Magento\Payment\Block\Form {

	protected $gatewayConfig;

	private $paymentDataHelper;

	public function __construct(Config $gatewayConfig,
	                            Data $paymentDataHelper) {
		$this->gatewayConfig = $gatewayConfig;
		$this->paymentDataHelper = $paymentDataHelper;
	}
}
