<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 elOOm (https://eloom.tech)
* @version      1.0.5
* @license      https://eloom.tech/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Block\Adminhtml\Config\Fieldset;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Module\ModuleResource;

class VersionHint extends Template implements RendererInterface {
	
	/**
	 * @var ModuleResource
	 */
	private $moduleResource;
	
	/**
	 * @param ModuleResource $moduleResource
	 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
	 * @SuppressWarnings(Magento.TypeDuplication)
	 */
	public function __construct(ModuleResource $moduleResource) {
		$this->moduleResource = $moduleResource;
	}
	
	/**
	 * @param AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element) {
		$dbVersion = (string)$this->moduleResource->getDbVersion('Eloom_PayU');
		
		$version = '<strong>' . $dbVersion . '</strong> - [<a href="https://github.com/eloom/module-payu/releases" target="_blank">' . __('Releases') . '</a>]';
		
		$html = '';
		$html .= sprintf('<tr id="row_%s">', $element->getHtmlId());
		$html .= '<td class="label">' . __($element->getLabel()) . '</td>';
		$html .= '<td>' . $version . '</td></tr>';
		
		return $html;
	}
}
