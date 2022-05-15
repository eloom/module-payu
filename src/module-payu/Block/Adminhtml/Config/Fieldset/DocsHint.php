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

namespace Eloom\PayU\Block\Adminhtml\Config\Fieldset;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class DocsHint extends Template implements RendererInterface {
	
	/**
	 * @param AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element) {
		$html = '';
		
		$html .= sprintf('<tr id="row_%s">', $element->getHtmlId());
		$html .= '<td class="label">'. __($element->getLabel()) .'</td>';
		$html .= '<td class="value">';
		$html .= sprintf(
			'<a href="%s" target="_blank">' . __('Documentation') . '</a>',
			'https://docs.eloom.tech/payment/payu-latam'
		);
		$html .= '</td></tr>';
		
		return $html;
	}
}
