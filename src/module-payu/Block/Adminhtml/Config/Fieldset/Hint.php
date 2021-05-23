<?php
/**
* 
* PayU Latam para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayU Latam
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.1.0
* @license      https://www.eloom.com.br/license
*
*/
declare(strict_types=1);

namespace Eloom\PayU\Block\Adminhtml\Config\Fieldset;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Hint extends Template implements RendererInterface {
	/**
	 * @var string
	 * @deprecated 100.1.0
	 */
	protected $_template = 'Eloom_PayU::config/fieldset/hint.phtml';
	
	/**
	 * @param AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element) {
		$html = '';
		
		if ($element->getComment()) {
			$html .= sprintf('<tr id="row_%s">', $element->getHtmlId());
			$html .= '<td colspan="1"><p class="note"><span>';
			$html .= sprintf(
				'<a href="%s" target="_blank">' . __('Documentation') . '</a>',
				$element->getComment()
			);
			$html .= '</span></p></td></tr>';
		}
		
		return $html;
	}
}
