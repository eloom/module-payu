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

namespace Eloom\PayU\Gateway\PayU\Exception;

class InvalidBuilderException extends PayUException {

	protected $message = 'The specified builder is not valid, the builder must be an implementation of BuilderInterface';
}