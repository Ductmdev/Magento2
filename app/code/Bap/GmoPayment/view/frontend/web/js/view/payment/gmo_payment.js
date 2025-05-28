define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'gmo_payment',
                component: 'Bap_GmoPayment/js/view/payment/method-renderer/cc-form'
            }
        );

        return Component.extend({});
    }
);