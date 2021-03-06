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

namespace Eloom\PayU\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

class Info extends ConfigurableInfo {

	/**
	 * @var string
	 */
	protected $_template = 'Eloom_PayU::info/default.phtml';

	public function getPaymentLink() {
		return null;
	}

	public function getPdfLink() {
		return null;
	}

	public function getBarCode() {
		return null;
	}

	public function getExpirationDate() {
		return null;
	}

	public function getQrCodeEmv() {
		return null;
	}

	public function getQrCodeImageBase64() {
		return null;
	}

	protected function getLabel($field) {
		return __($field);
	}

	public function getSyncronizeUrl() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$urlBuilder = $objectManager->get('\Magento\Backend\Model\UrlInterface');
		$url = $urlBuilder->getUrl('eloompayu/payment/syncronize', ['order_id' => $this->getInfo()->getOrder()->getId()]);

		return $url;
	}

	public function getTransactionState() {
		$state = $this->getInfo()->getPayuTransactionState();
		return __('Transaction.State.' . $state);
	}

	public function getIconTransactionState() {
		return $this->getInfo()->getPayuTransactionState();
	}

	public function getOrderId() {
		$payuOrderId = $this->getInfo()->getAdditionalInformation('payuOrderId');
		if (!empty($payuOrderId)) {
			return $payuOrderId;
		}
	}

	public function getTransactionId() {
		$transactionId = $this->getInfo()->getAdditionalInformation('transactionId');
		if (!empty($transactionId)) {
			return $transactionId;
		}
	}

	protected function _prepareSpecificInformation($transport = null) {
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		$transport = parent::_prepareSpecificInformation($transport);
		$data = array();

		$responseErrors = $this->getInfo()->getAdditionalInformation('responseErrors');
		if (!empty($responseErrors)) {
			$data[(string)__('Error')] = $responseErrors;
		}

		return $transport->setData(array_merge($data, $transport->getData()));
	}
}
