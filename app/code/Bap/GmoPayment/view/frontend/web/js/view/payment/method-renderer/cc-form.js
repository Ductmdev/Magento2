define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/model/quote',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component, $, additionalValidators, redirectOnSuccessAction, quote) {
        'use strict';
        return Component.extend({
            defaults: {
                code: 'gmo_payment',
                template: 'Bap_GmoPayment/payment/cc-form',
                creditCardHolderName: ''
            },
            initObservable: function () {
                this._super()
                    .observe(['creditCardHolderName']);
                
                return this;
            },
            getData: function () {
                let data = this._super();
                data.additional_data = Object.assign({}, data.additional_data, {
                    cc_holder_name: this.creditCardHolderName()
                });

                return data;
            },
            getSelector: function (field) {
                return '#' + this.getCode() + '_' + field;
            },
            validate: function () {
                const form = $(this.getSelector('payment-form'));
                form.validation();

                return form.valid();
            },
            getCode: function () {
                return this.code;
            },
            isActive: function () {
                return this.getCode() === this.isChecked();
            },
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() &&
                    additionalValidators.validate() &&
                    this.isPlaceOrderActionAllowed() === true
                ) {
                    this.isPlaceOrderActionAllowed(false);

                    this.getPlaceOrderDeferredObject()
                        .done(
                            function (response) {
                                const orderId = response;
                                const isThreeDSecureEnabled = window.checkoutConfig.payment.gmo_payment.three_d_secure.enabled;
                                const threeDSecureUrl = window.checkoutConfig.payment.gmo_payment.three_d_secure.three_d_secure_url;

                                if (!isThreeDSecureEnabled && self.redirectAfterPlaceOrder) {
                                    redirectOnSuccessAction.execute();
                                }

                                $.ajax({
                                    url: threeDSecureUrl.replace(':orderId', orderId),
                                    type: 'GET',
                                    success: function (response) {
                                        if (response) {
                                            window.location.href = response;
                                        }
                                    }
                                });
                            }
                        ).always(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                            }
                        );

                    return true;
                }

                return false;
            },
        });
    }
);