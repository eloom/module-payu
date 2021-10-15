<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://eloom.tech)
* @version      1.0.2
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Cron;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use Eloom\PayU\Model\PaymentManagement\Processor;
use Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState;

class SyncronizeOrderPaymentReviewState {

	private $orderRepository;

	private $searchCriteriaBuilder;

	private $logger;

	public function __construct(LoggerInterface $logger,
	                            OrderRepository $orderRepository,
	                            SearchCriteriaBuilder $searchCriteriaBuilder) {
		$this->logger = $logger;
		$this->orderRepository = $orderRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
	}

	public function execute() {
		$filter = new Filter();
		$filter->setField('method')->setValue('eloom_payments_payu_%')->setConditionType('like');

		$filterPaymentMethodGroup = new FilterGroup();
		$filterPaymentMethodGroup->setFilters([$filter]);

		$filterPaymentStatus = new Filter();
		$filterPaymentStatus->setField('payu_transaction_state')->setValue(PayUTransactionState::APPROVED()->key())->setConditionType('eq');

		$filterPaymentStatusGroup = new FilterGroup();
		$filterPaymentStatusGroup->setFilters([$filterPaymentStatus]);

		$filterOrderState = new Filter();
		$filterOrderState->setField('state')->setValue(Order::STATE_PAYMENT_REVIEW)->setConditionType('eq');

		$filterOrderGroup = new FilterGroup();
		$filterOrderGroup->setFilters([$filterOrderState]);

		$searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$filterOrderGroup, $filterPaymentStatusGroup, $filterPaymentMethodGroup])->create();
		$collection = $this->orderRepository->getList($searchCriteria);
		
		//$this->logger->info($collection->getSelect()->__toString());
		
		$orderList = $collection->getItems();

		if (count($orderList)) {
			$processor = ObjectManager::getInstance()->get(Processor::class);
			foreach ($orderList as $order) {
				try {
					$this->logger->info(sprintf("%s - Synchronizing Order %s", __METHOD__, $order->getIncrementId()));
					$processor->syncronize($order->getPayment(), false, $order->getGrandTotal());
				} catch (\Exception $e) {
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
				}
			}
		}
	}
}