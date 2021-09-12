<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.1
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Controller\Adminhtml\Payment;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;

class Syncronize extends \Magento\Sales\Controller\Adminhtml\Order implements HttpPostActionInterface {

	public function execute() {
		$order = $this->_initOrder();
		$response = null;
		if ($order) {
			try {
				$processor = ObjectManager::getInstance()->get(Processor::class);
				$processor->syncronize($order->getPayment(), false, $order->getGrandTotal());

				return $this->resultPageFactory->create();
			} catch (Exception $e) {
				$response = ['error' => true, 'message' => __('We cannot syncronize payment order.')];
			}
			if (is_array($response)) {
				$resultJson = $this->resultJsonFactory->create();
				$resultJson->setData($response);
				return $resultJson;
			}
		}

		return $this->resultRedirectFactory->create()->setPath('sales/*/');
	}
}
