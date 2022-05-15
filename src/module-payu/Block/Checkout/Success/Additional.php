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

namespace Eloom\PayU\Block\Checkout\Success;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;

class Additional extends \Magento\Framework\View\Element\Template {
	
	private $paymentMethod;
	private $checkoutSession;
	protected $orderFactory;
	protected $order;
	
	protected $vouchers = [
		'eloom_payments_payu_boleto',
		'eloom_payments_payu_baloto',
		'eloom_payments_payu_efecty',
		'eloom_payments_payu_pagoefectivo',
		'eloom_payments_payu_oxxo',
		'eloom_payments_payu_seveneleven',
		'eloom_payments_payu_multicaja',
		'eloom_payments_payu_pagofacil',
		'eloom_payments_payu_rapipago'
	];
	
	public function __construct(Context      $context,
	                            Session      $checkoutSession,
	                            OrderFactory $orderFactory,
	                            array        $data = []) {
		parent::__construct($context, $data);
		$this->checkoutSession = $checkoutSession;
		$this->orderFactory = $orderFactory;
		
		if (!$this->isVoucher() && !$this->isCc()) {
			return;
		}
		$this->setTemplate("Eloom_PayU::checkout/success/additional.phtml");
	}
	
	public function getPayment() {
		if ($this->paymentMethod == null) {
			$this->paymentMethod = $this->getOrder()->getPayment();
		}
		
		return $this->paymentMethod;
	}
	
	public function getOrder() {
		if ($this->order == null) {
			$this->order = $this->orderFactory->create()->load($this->checkoutSession->getLastOrderId());
		}
		return $this->order;
	}
	
	public function isVoucher(): bool {
		$method = $this->getPayment()->getMethod();
		if (array_key_exists($method, $this->vouchers)) {
			return true;
		}
		
		return false;
	}
	
	public function isCc(): bool {
		$method = $this->getPayment()->getMethod();
		if ($method == \Eloom\PayU\Model\Ui\Cc\ConfigProvider::CODE) {
			return true;
		}
		
		return false;
	}
	
	public function getPaymentLink() {
		$link = $this->getPayment()->getAdditionalInformation('paymentLink');
		if ($link) {
			return $link;
		}
		
		return null;
	}
	
	public function getBankUrl() {
		$url = $this->getPayment()->getAdditionalInformation('bankUrl');
		if ($url) {
			return $url;
		}
		
		return null;
	}
	
	public function getPdfLink() {
		$pdfLink = $this->getPayment()->getAdditionalInformation('pdfLink');
		if ($pdfLink) {
			return $pdfLink;
		}
		
		return null;
	}
	
	public function getOrderId() {
		$payuOrderId = $this->getPayment()->getAdditionalInformation('payuOrderId');
		if ($payuOrderId) {
			return $payuOrderId;
		}
		return null;
	}
	
	public function getTransactionId() {
		$transactionId = $this->getPayment()->getAdditionalInformation('transactionId');
		if ($transactionId) {
			return $transactionId;
		}
		return null;
	}
	
	public function getBarcode() {
		$barCode = $this->getPayment()->getAdditionalInformation('barCode');
		if ($barCode) {
			return $barCode;
		}
		
		return null;
	}
	
	public function getCcType() {
		$value = $this->getPayment()->getCcType();
		if ($value) {
			return $value;
		}
		
		return null;
	}
	
	public function getCcLast4() {
		$value = $this->getPayment()->getCcLast4();
		if ($value) {
			return $value;
		}
		
		return null;
	}
	
	public function getPayuTransactionState() {
		$value = $this->getPayment()->getPayuTransactionState();
		if ($value) {
			return $value;
		}
		
		return null;
	}
	
	public function getFormattedInstallmentAmount() {
		$objectManager = ObjectManager::getInstance();
		$priceCurrency = $objectManager->create('Magento\Framework\Pricing\PriceCurrencyInterface');
		
		$installmentAmount = $this->getPayment()->getAdditionalInformation('installmentAmount');
		$installments = $this->getPayment()->getAdditionalInformation('installments');
		
		return __("In %1x of %2", $installments, $priceCurrency->format($installmentAmount, false));
	}
	
	public function hasInstallments() {
		$installments = $this->getPayment()->getAdditionalInformation('installments');
		
		return (null != $installments && !empty($installments));
	}
	
}