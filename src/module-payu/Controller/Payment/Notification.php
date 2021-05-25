<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.0.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Controller\Payment;

use Eloom\Core\Enumeration\HttpStatus;
use Eloom\PayU\Model\NotificationFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Payment\Gateway\Command\CommandException;
use Psr\Log\LoggerInterface;

class Notification extends Action implements CsrfAwareActionInterface {

	private $logger;

	private $notificationFactory;

	private $connectionPool;

	public function __construct(Context $context,
															LoggerInterface $logger,
															NotificationFactory $notificationFactory,
															ResourceConnection $connectionPool = null) {

		parent::__construct($context);

		$this->logger = $logger;
		$this->notificationFactory = $notificationFactory;
		$this->connectionPool = $connectionPool ?: ObjectManager::getInstance()->get(ResourceConnection::class);
	}

	public function execute() {
		$data = $this->getRequest()->getPostValue();
		$response = HttpStatus::UNPROCESSABLE_ENTITY()->getCode();
		$connection = $this->connectionPool->getConnection('sales');

		try {
			$this->logger->info(sprintf("%s - Receiving PayU notification [%s]", __METHOD__, json_encode($data)));

			if ($data && isset($data['reference_sale']) && isset($data['state_pol'])) {
				$connection->beginTransaction();

				$notification = $this->notificationFactory->create();
				$notification->setIncrementId($data['reference_sale']);
				$notification->save();

				$connection->commit();

				$response = HttpStatus::OK()->getCode();
			}
		} catch (CommandException $e) {
			$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
			$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getTraceAsString()));
			$connection->rollBack();

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
