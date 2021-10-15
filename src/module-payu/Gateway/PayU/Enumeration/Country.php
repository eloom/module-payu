<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.2
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\PayU\Enumeration;

use Eloom\Core\Lib\Enumeration\AbstractMultiton;

class Country extends AbstractMultiton {
	
	public function getCode() {
		return $this->code;
	}
	
	public function getCurrency() {
		return $this->currency;
	}
	
	public function getLanguage() {
		return $this->language;
	}
	
	public function isArgentina(): bool {
		return $this->code == 'AR';
	}
	
	public function isBrazil(): bool {
		return $this->code == 'BR';
	}
	
	public function isChile(): bool {
		return $this->code == 'CL';
	}
	
	public function isColombia(): bool {
		return $this->code == 'CO';
	}
	
	public function isMexico(): bool {
		return $this->code == 'MX';
	}
	
	public function isPanama(): bool {
		return $this->code == 'PA';
	}
	
	public function isPeru(): bool {
		return $this->code == 'PE';
	}
	
	protected static function initializeMembers() {
		new static('ARS', 'AR', 'ARS', 'es');
		new static('BRL', 'BR', 'BRL', 'pt');
		new static('CLP', 'CL', 'CLP', 'es');
		new static('COP', 'CO', 'COP', 'es');
		new static('MXN', 'MX', 'MXN', 'es');
		new static('PAB', 'PA', 'USD', 'es');
		new static('PEN', 'PE', 'PEN', 'es');
	}
	
	protected function __construct($key, $code, $currency, $language) {
		parent::__construct($key);
		
		$this->code = $code;
		$this->currency = $currency;
		$this->language = $language;
	}
	
	private $code;
	private $currency;
	private $language;
}