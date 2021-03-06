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

namespace Eloom\PayU\Gateway\PayU;

class CommandInterface {

	/**
	 * Commands available in all API's
	 */
	const PING = 'PING';

	/**
	 * Commands available in Payment API
	 */
	const PAYMENT_GET_BANKS_LIST = 'GET_BANKS_LIST';
	const PAYMENT_GET_PAYMENT_METHODS = 'GET_PAYMENT_METHODS';
	const PAYMENT_SUBMIT_TRANSACTION = 'SUBMIT_TRANSACTION';

	/**
	 * Commands available in Query API
	 */
	const QUERY_ORDER_DETAIL = 'ORDER_DETAIL';
	const QUERY_ORDER_DETAIL_BY_REFERENCE_CODE = 'ORDER_DETAIL_BY_REFERENCE_CODE';
	const QUERY_TRANSACTION_RESPONSE_DETAIL = 'TRANSACTION_RESPONSE_DETAIL';
}