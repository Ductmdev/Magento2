define([
    'postalCodeDataProvider',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'uiRegistry',
    'mage/translate',
    'underscore'
], function (postalCodeDataProvider, fullScreenLoader, validator, registry, $t, _) {
    'use strict';

    var updateRegion = function (regionSelectComponent, regionTextComponent, newValue) {
        if (regionSelectComponent.visible()) {
            var option = _.find(regionSelectComponent.options(), function (option) {
                return option.title.trim().toLowerCase() === newValue.trim().toLowerCase() ||
                    option.label.trim().toLowerCase() === newValue.trim().toLowerCase();
            });

            regionSelectComponent.value(option ? option.value : '');
            regionSelectComponent.trigger('input');
        } else {
            regionTextComponent.value(newValue);
        }
    },
        updateStreet = function (component, newValue) {
        if (component.value().indexOf(newValue) !== 0) {
            component.value(newValue);
        }
    };

    return function (target) {
        var loadPostalCodeData = postalCodeDataProvider(document.documentElement.lang || 'ja');

        return target.extend({
            initialUpdateHandled: false,
            processingDelay: validator.validateDelay + 1,
            scheduledHandler: null,

            onUpdate: function () {
                var element = this;

                element._super();

                if (!element.initialUpdateHandled) {
                    element.initialUpdateHandled = true;
                    return;
                }

                if (element.scheduledHandler) {
                    clearTimeout(element.scheduledHandler);
                }

                element.warn(null);
                element.scheduledHandler = setTimeout(function () {
                    if (!element.warn()) {
                        element.updateRelatedAddressComponents();
                    }
                }, element.processingDelay);
            },

            updateRelatedAddressComponents: function () {
                var postalCodeComponent = this,
                    addressComponentName = postalCodeComponent.parentName,
                    countryComponent = registry.get(addressComponentName + '.' + 'country_id');

                if (countryComponent.value() !== 'JP') {
                    postalCodeComponent.warn(null);
                    return;
                }

                fullScreenLoader.startLoader();

                loadPostalCodeData(postalCodeComponent.value()).then(
                    function (data) {
                        var regionSelectComponent = registry.get(addressComponentName + '.' + 'region_id'),
                            regionTextComponent = registry.get(addressComponentName + '.' + 'region_id_input'),
                            cityComponent = registry.get(addressComponentName + '.' + 'city'),
                            street1Component = registry.get(addressComponentName + '.' + 'street.0'),
                            street2Component = registry.get(addressComponentName + '.' + 'street.1');

                        updateRegion(regionSelectComponent, regionTextComponent, data.region || '');
                        cityComponent.value(data.city || '');
                        updateStreet(street1Component, data.street1 || '');
                        updateStreet(street2Component, data.street2 || '');

                        postalCodeComponent.warn(null);
                        fullScreenLoader.stopLoader();
                    },
                    function (reason) {
                        postalCodeComponent.warn(
                            reason + ' ' + $t('If you believe it is the right one you can ignore this notice.')
                        );
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        });
    };
});
