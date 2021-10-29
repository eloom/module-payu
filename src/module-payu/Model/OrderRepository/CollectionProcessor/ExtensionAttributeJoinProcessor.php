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

namespace Eloom\PayU\Model\OrderRepository\CollectionProcessor;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class ExtensionAttributeJoinProcessor implements CollectionProcessorInterface {

	private $joinProcessor;

	public function __construct(JoinProcessorInterface $joinProcessor) {
		$this->joinProcessor = $joinProcessor;
	}

	public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection) {
		$this->joinProcessor->process($collection);
	}
}