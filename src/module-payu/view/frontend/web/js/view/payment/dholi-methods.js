define([
	'uiComponent',
	'Magento_Checkout/js/model/payment/renderer-list'
	],
	function (Component, rendererList) {
		'use strict';

		rendererList.push({
			type: 'eloom_payments_payu_cc',
			component: 'Eloom_PayU/js/view/payment/method-renderer/cc-method'
		});

		return Component.extend({});
	}
);