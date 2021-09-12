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

namespace Eloom\PayU\Cron;

use Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Eloom\PayU\Model\PaymentManagement\Processor;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Payment\Repository;
use Psr\Log\LoggerInterface;

class SyncronizePaymentInPendingState {

	private $paymentRepository;

	private $searchCriteriaBuilder;

	private $logger;

	public function __construct(LoggerInterface $logger,
	                            Repository $paymentRepository,
	                            SearchCriteriaBuilder $searchCriteriaBuilder) {
		$this->logger = $logger;
		$this->paymentRepository = $paymentRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
	}

	public function execute() {
		$filter = new Filter();
		$filter->setField('method')->setValue('eloom_payments_payu_%')->setConditionType('like');

		$paymentGroup = new FilterGroup();
		$paymentGroup->setFilters([$filter]);

		// status
		$filterStatus = new Filter();
		$filterStatus->setField('payu_transaction_state')->setValue(PayUTransactionState::PENDING()->key())->setConditionType('eq');

		$statusGroup = new FilterGroup();
		$statusGroup->setFilters([$filterStatus]);

		$searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$paymentGroup, $statusGroup])->create();
		$paymentList = $this->paymentRepository->getList($searchCriteria)->getItems();

		if (count($paymentList)) {
			$processor = ObjectManager::getInstance()->get(Processor::class);
			foreach ($paymentList as $payment) {
				try {
					$this->logger->info(sprintf("%s - Synchronizing - Order %s", __METHOD__, $payment->getOrder()->getIncrementId()));
					$processor->syncronize($payment, false, $payment->getOrder()->getGrandTotal());
				} catch (\Exception $e) {
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
				}
			}
		}
	}
}