define([
    'postalCodeDataProvider',
    'jquery',
    'mage/translate'
], function (postalCodeDataProvider, $, $t) {
    'use strict';

    var updateRegion = function (regionIdElement, regionNameElement, region) {
        if (regionIdElement.is(':visible')) {
            var option = regionIdElement.find('option').filter(function () {
                return $(this).text().trim().toLowerCase() === region.trim().toLowerCase();
            }).first();

            var newValue = option.length ? option.val() : '';
            if (regionIdElement.val() !== newValue) {
                regionIdElement.val(newValue).trigger('change');
            }
        } else {
            regionNameElement.val(region).trigger('input');
        }
    };

    var updateStreet = function (element, newValue) {
        if (newValue && !element.val().startsWith(newValue)) {
            element.val(newValue).trigger('input');
        }
    };

    var renderAlertMessage = function (element, message) {
        var messageContainer = element.next('div[role="alert"]');

        if (!messageContainer.length) {
            messageContainer = $('<div role="alert" class="message warning" style="display:none"><span></span></div>')
                .insertAfter(element);
        }

        messageContainer.children(':first').text(message ? message + ' ' + $t('If you believe it is the right one you can ignore this notice.') : '');
        messageContainer.toggle(!!message);
    };

    $.widget('magentoJapaneseAddress.addressUpdater', {
        _create: function () {
            var options = this.options,
                regionId = $(options.regionId),
                region = $(options.region),
                city = $(options.city),
                street1 = $(options.street1),
                street2 = $(options.street2),
                loadPostalCodeData = postalCodeDataProvider(options.lang || document.documentElement.lang || 'ja');

            $(this.element).on('input', function (event) {
                var postalCode = event.target.value.trim();
                if (!postalCode) return;

                $.when(loadPostalCodeData(postalCode))
                    .done(function (data) {
                        updateRegion(regionId, region, data.region);
                        region.val(data.region).trigger('input');
                        city.val(data.city).trigger('input');
                        updateStreet(street1, data.street1);
                        updateStreet(street2, data.street2);
                        renderAlertMessage($(event.target), '');
                    })
                    .fail(function (reason) {
                        renderAlertMessage($(event.target), reason);
                    });
            });
        }
    });

    return $.magentoJapaneseAddress.addressUpdater;
});
