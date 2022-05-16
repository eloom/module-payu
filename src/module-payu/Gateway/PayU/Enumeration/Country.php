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

namespace Eloom\PayU\Gateway\PayU\Enumeration;

use Eloom\Core\Lib\Enumeration\AbstractMultiton;

class Country extends AbstractMultiton {

	public function getCurrency() {
		return $this->currency;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function isArgentina($code): bool {
		return $code == 'AR';
	}

	public function isBrazil($code): bool {
		return $code == 'BR';
	}

	public function isChile($code): bool {
		return $code == 'CL';
	}

	public function isColombia($code): bool {
		return $code == 'CO';
	}

	public function isMexico($code): bool {
		return $code == 'MX';
	}

	public function isPanama($code): bool {
		return $code == 'PA';
	}

	public function isPeru($code): bool {
		return $code == 'PE';
	}

	protected static function initializeMembers() {
		new static('ARS', 'ARS', 'es');
		new static('BRL', 'BRL', 'pt');
		new static('CLP', 'CLP', 'es');
		new static('COP', 'COP', 'es');
		new static('MXN', 'MXN', 'es');
		new static('PAB', 'USD', 'es');
		new static('PEN', 'PEN', 'es');
		new static('USD', 'USD', 'en');
	}

	protected function __construct($key, $currency, $language) {
		parent::__construct($key);

		$this->currency = $currency;
		$this->language = $language;
	}

	private $currency;
	private $language;
}