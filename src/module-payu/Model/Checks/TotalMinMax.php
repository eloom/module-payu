<?php
##eloom.local##
namespace Eloom\PayU\Model\Checks;

use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;

class TotalMinMax extends \Magento\Payment\Model\Checks\TotalMinMax {

	public function isApplicable(MethodInterface $paymentMethod, Quote $quote) {
		$total = $quote->getGrandTotal();
		$minTotal = $paymentMethod->getConfigData(self::MIN_ORDER_TOTAL);
		$maxTotal = $paymentMethod->getConfigData(self::MAX_ORDER_TOTAL);
		if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
			return false;
		}
		return true;
	}
}
