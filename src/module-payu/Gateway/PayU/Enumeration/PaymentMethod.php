<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.0.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\PayU\Enumeration;

use Eloom\Core\Lib\Enumeration\AbstractMultiton;

class PaymentMethod extends AbstractMultiton {

	public function getCode() {
		return $this->code;
	}
	
	protected static function initializeMembers() {
		/**
		 * Credit Card
		 */
		new static('visa', 'VISA');
		new static('mastercard', 'MASTERCARD');
		new static('amex', 'AMEX');
		new static('dinersclub', 'DINERS');
		
		new static('elo', 'ELO');
		new static('hipercard', 'HIPERCARD');
		new static('naranja', 'NARANJA');
		new static('shopping', 'SHOPPING');
		new static('cabal', 'CABAL');
		new static('agendcard', 'AGENDCARD');
		new static('cencosud', 'CENCOSUD');
		new static('credencial', 'CREDENCIAL');
		new static('cmr', 'CMR');
		new static('codensa', 'CODENSA');
		
		/**
		 * AR
		 */
		new static('pagofacil', 'PAGOFACIL');
		new static('rapipago', 'RAPIPAGO');
		
		/**
		 * BR
		 */
		new static('boleto', 'BOLETO_BANCARIO');
		
		/**
		 * CL
		 */
		new static('multicaja', 'MULTICAJA');

		/**
		 * CO
		 */
		new static('baloto', 'BALOTO');
		new static('efecty', 'EFECTY');
		new static('sured', 'SURED');
		
		/**
		 * MX
		 */
		new static('oxxo', 'OXXO');
		new static('seveneleven', 'SEVEN_ELEVEN');
		new static('spei', 'SPEI');
		
		/**
		 * PE
		 */
		new static('pagoefectivo', 'PAGOEFECTIVO');
	}

	protected function __construct($key, $code) {
		parent::__construct($key);

		$this->code = $code;
	}

	private $code;
}