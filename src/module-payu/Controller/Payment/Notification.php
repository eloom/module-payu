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

namespace Eloom\PayU\Controller\Payment;

use Eloom\Core\Enumeration\HttpStatus;
use Eloom\PayU\Model\NotificationFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Psr\Log\LoggerInterface;

class Notification extends Action implements CsrfAwareActionInterface {
	
	private $logger;
	
	private $notificationFactory;
	
	public function __construct(Context             $context,
	                            LoggerInterface     $logger,
	                            NotificationFactory $notificationFactory) {
		
		parent::__construct($context);
		
		$this->logger = $logger;
		$this->notificationFactory = $notificationFactory;
	}
	
	public function execute() {
		$data = $this->getRequest()->getPostValue();
		$response = HttpStatus::UNPROCESSABLE_ENTITY()->getCode();
		
		try {
			$this->logger->info(sprintf("%s - Receiving PayU notification [%s]", __METHOD__, json_encode($data)));
			
			if ($data && isset($data['reference_sale']) && isset($data['state_pol'])) {
				$notification = $this->notificationFactory->create();
				$notification->setIncrementId($data['reference_sale']);
				$notification->save();
				
				$response = HttpStatus::OK()->getCode();
			}
		} catch (CommandException $e) {
			$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
			$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getTraceAsString()));
			
			$response = HttpStatus::INTERNAL_SERVER_ERROR()->getCode();
		}
		$this->logger->info(sprintf("%s - PayU notification OK. HTTP Code Response [%s].", __METHOD__, $response));
		
		$this->getResponse()->clearHeader('Content-Type')->setHeader('Content-Type', 'text/html')->setHttpResponseCode($response)->setBody($response);
		
		return;
	}
	
	public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException {
		return null;
	}
	
	public function validateForCsrf(RequestInterface $request): ?bool {
		return true;
	}
}
