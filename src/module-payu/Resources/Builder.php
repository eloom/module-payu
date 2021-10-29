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

namespace Eloom\PayU\Resources;

class Builder {

	const FILE = __DIR__ . '/resources.yaml';

	private static $instance;

	private static $data;

	private static $path;

	private static $services;

	private function __construct() {
	}

	public static function getInstance(): \Eloom\PayU\Resources\Builder {
		if (!isset(self::$instance)) {
			self::$instance = new \Eloom\PayU\Resources\Builder();

			self::$data = \Symfony\Component\Yaml\Yaml::parseFile(self::FILE);

			self::$path = self::$data['resources']['path'];
			self::$services = self::$data['resources']['services'];
		}

		return self::$instance;
	}

	public static function getApplicationId(): int {
		if (!isset(self::$instance)) {
			self::getInstance();
		}
		return self::$data['aplication'];
	}

	public static function getUrl($resource, $environment): string {
		if (!isset(self::$instance)) {
			self::getInstance();
		}

		return sprintf("%s://%s", self::$path['protocol'], self::$path[$resource]['environment'][$environment]);
	}

	public static function getApiUrl($environment): string {
		if (!isset(self::$instance)) {
			self::getInstance();
		}
		return self::getUrl('api', $environment);
	}

	public static function getService($url, $service) {
		if (!isset(self::$instance)) {
			self::getInstance();
		}
		return sprintf("%s/%s", $url, self::$services[$service]);
	}
}