<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Gateway\PayU\Exception;

class InvalidEnvironmentException extends PayUException {

	protected $message = 'The environment must be an environment constant of PayU class or an implementation of EnvironmentInterface';
}