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

namespace Eloom\PayU\Model;

use Eloom\PayU\Api\TotalsInterface;
use Eloom\PayU\Model\Ui\Cc\ConfigProvider;
use Eloom\PayU\Plugin\Discount;
use Eloom\PayU\Plugin\Interest;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\PaymentInterface;

class Totals implements TotalsInterface {
	
	private $serializer;
	
	/**
	 * @var Cart
	 */
	protected $cart;
	
	protected $discountPlugin;
	
	protected $interestPlugin;
	
	public function __construct(Json $serializer = null,
	                            Cart $cart,
	                            Discount $discount,
	                            Interest $interest) {
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->cart = $cart;
		$this->discountPlugin = $discount;
		$this->interestPlugin = $interest;
	}
	
	/**
	 * @inheritDoc
	 */
	public function reload(PaymentInterface $paymentMethod, $shippingAmount) {
		if (isset($paymentMethod)) {
			$methodCode = $paymentMethod->getMethod();
			
			if (ConfigProvider::CODE == $methodCode || ConfigProvider::CC_VAULT_CODE == $methodCode) {
				$quote = $this->cart->getQuote();
				$quote->getPayment()->setAdditionalInformation('invokedTotals', $methodCode);
				
				if ($paymentMethod->getAdditionalData()) {
					$installments = $paymentMethod->getAdditionalData()['installments'];
					if ($installments) {
						$arrayex = explode('-', $installments);
						$installment = intval($arrayex[0]);
						$baseCurrencyCode = $quote->getBaseCurrencyCode();
						
						if ($installment == 1) {
							$quote->setNeedApplyEloomPayUDiscount(true);
							
							$paymentDiscount = $this->discountPlugin->getDiscount($quote, $baseCurrencyCode, $installment);
							
							$quote->setEloomPayUDiscount($paymentDiscount);
						} else {
							$quote->setNeedApplyEloomPayUInterest(true);
							
							$paymentInterest = $this->interestPlugin->getInterest($quote, $baseCurrencyCode, $installment);
							
							$quote->setEloomPayUInterest($paymentInterest);
						}
					} else {
						$quote->setNeedResetEloomPayUDiscount(true);
						$quote->setNeedResetEloomPayUInterest(true);
					}
				}
				$quote->setTotalsCollectedFlag(false)->collectTotals()->save();
				
				$quote->setNeedApplyEloomPayUDiscount(false);
				$quote->setNeedApplyEloomPayUInterest(false);
				
				$quote->setNeedResetEloomPayUDiscount(false);
				$quote->setNeedResetEloomPayUInterest(false);
			}
		}
		
		return $this->serializer->serialize(['success' => true]);
	}
}