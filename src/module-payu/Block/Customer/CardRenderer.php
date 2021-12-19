<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.4
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Block\Customer;

use Eloom\PayU\Model\Ui\Cc\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Block\AbstractCardRenderer;
use Magento\Payment\Model\CcConfigProvider;

class CardRenderer extends AbstractCardRenderer {

	protected $configProvider;

	public function __construct(Template\Context $context,
															CcConfigProvider $iconsProvider,
															ConfigProvider $configProvider,
															array $data) {
		parent::__construct($context, $iconsProvider, $data);

		$this->configProvider = $configProvider;
	}

	public function canRender(PaymentTokenInterface $token) {
		return $token->getPaymentMethodCode() === $this->configProvider::CODE;
	}

	public function getNumberLast4Digits() {
		return $this->getTokenDetails()['maskedCC'];
	}

	public function getExpDate() {
		return $this->getTokenDetails()['expirationDate'];
	}

	public function getIconUrl() {
		$url = null;
		if (isset($this->configProvider->getConfig()['payment'][$this->configProvider::CODE]['icons']['brands'][$this->getTokenDetails()['type']])) {
			$url = $this->configProvider->getConfig()['payment'][$this->configProvider::CODE]['icons']['brands'][$this->getTokenDetails()['type']];
		}

		return $url;
	}

	public function getIconHeight() {
		return 'auto';
	}

	public function getIconWidth() {
		return '40px';
	}
}
