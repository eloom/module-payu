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

namespace Eloom\PayU\Block\Adminhtml\Config;

class Fieldset extends \Magento\Config\Block\System\Config\Form\Fieldset {

	protected $_backendConfig;

	public function __construct(
		\Magento\Backend\Block\Context $context,
		\Magento\Backend\Model\Auth\Session $authSession,
		\Magento\Framework\View\Helper\Js $jsHelper,
		\Magento\Config\Model\Config $backendConfig,
		array $data = []
	) {
		$this->_backendConfig = $backendConfig;
		parent::__construct($context, $authSession, $jsHelper, $data);
	}

	protected function _getFrontendClass($element) {
		$enabledString = $this->_isPaymentEnabled($element) ? ' enabled' : '';
		return parent::_getFrontendClass($element) . ' with-button' . $enabledString;
	}

	protected function _isPaymentEnabled($element) {
		$groupConfig = $element->getGroup();
		$activityPaths = isset($groupConfig['activity_path']) ? $groupConfig['activity_path'] : [];

		if (!is_array($activityPaths)) {
			$activityPaths = [$activityPaths];
		}

		$isPaymentEnabled = false;
		foreach ($activityPaths as $activityPath) {
			$isPaymentEnabled = $isPaymentEnabled
				|| (bool)(string)$this->_backendConfig->getConfigDataValue($activityPath);
		}

		return $isPaymentEnabled;
	}

	protected function _getHeaderTitleHtml($element) {
		$html = '<div class="config-heading" >';
		$disabledAttributeString = $this->_isPaymentEnabled($element) ? '' : ' disabled="disabled"';
		$disabledClassString = $this->_isPaymentEnabled($element) ? '' : ' disabled';
		$htmlId = $element->getHtmlId();

		$html .= '<div class="button-container"><button type="button"' . $disabledAttributeString . ' 
		class="button action-configure' . $disabledClassString . '" 
		id="' . $htmlId . '-head" 
		onclick="payuLatamToggleSolution.call(this, \'' . $htmlId . "', '" . $this->getUrl('adminhtml/*/state') . '\'); return false;">
		<span class="state-closed">' . __('Configure') . '</span>
		<span class="state-opened">' . __('Close') . '</span></button>';

		$html .= '</div>';
		$html .= '<div class="heading"><strong>' . $element->getLegend() . '</strong>';

		if ($element->getComment()) {
			$html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
		}
		$html .= '<div class="payments"></div>';
		$html .= '</div></div>';

		return $html;
	}

	protected function _getHeaderCommentHtml($element) {
		return '';
	}

	protected function _isCollapseState($element) {
		return false;
	}

	protected function _getExtraJs($element) {
		$script = "require(['jquery', 'prototype'], function(jQuery){
            window.payuLatamToggleSolution = function (id, url) {
                var doScroll = false;
                Fieldset.toggleCollapse(id, url);
                if ($(this).hasClassName(\"open\")) {
                    $$(\".with-button button.button\").each(function(anotherButton) {
                        if (anotherButton != this && $(anotherButton).hasClassName(\"open\")) {
                            $(anotherButton).click();
                            doScroll = true;
                        }
                    }.bind(this));
                }
                if (doScroll) {
                    var pos = Element.cumulativeOffset($(this));
                    window.scrollTo(pos[0], pos[1] - 45);
                }
            }
        });";

		return $this->_jsHelper->getScript($script);
	}
}
