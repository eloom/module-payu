define(["Magento_Checkout/js/view/summary/abstract-total","Magento_Checkout/js/model/quote","Magento_Catalog/js/price-utils","Magento_Checkout/js/model/totals"],function(c,d,e,b){return c.extend({defaults:{isFullTaxSummaryDisplayed:window.checkoutConfig.isFullTaxSummaryDisplayed||!1,template:"Eloom_PayU/checkout/summary/interest"},totals:d.getTotals(),isTaxDisplayedInGrandTotal:window.checkoutConfig.includeTaxInGrandTotal||!1,isDisplayed:function(){return this.isFullMode()&&0<this.getPureValue()},
getValue:function(){var a=0;this.totals()&&(a=b.getSegment("eloom_interest").value);return this.getFormattedPrice(a)},getPureValue:function(){var a=0;this.totals()&&(a=b.getSegment("eloom_interest").value);return a}})});
