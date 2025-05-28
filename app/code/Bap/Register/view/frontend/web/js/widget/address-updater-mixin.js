/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'postalCodeDataProvider',
    'jquery',
    'mage/translate'
], function (postalCodeDataProvider, $, $t) {
    return function (addressUpdater) {
        var updateRegion = function (regionIdElement, regionNameElement, region) {
            regionIdElement.val(
                regionIdElement.find('option:contains("' + region + '")').filter(function (_, option) {
                    return $(option).text() === region;
                }).val()
            );
        },
        updateStreet = function (element, newValue) {
            if (element.val().indexOf(newValue) === 0) {
                return;
            }
            element.val(newValue);
        };

        $.widget('magentoJapanesePostalCode.addressUpdater', addressUpdater, {
            _create: function () {
                var regionId = $(this.options.regionId),
                    region = $(this.options.region),
                    city = $(this.options.city),
                    street1 = $(this.options.street1),
                    street2 = $(this.options.street2),
                    loadPostalCodeData = postalCodeDataProvider(this.options.lang || document.documentElement.lang || 'ja');

                $(this.element).on('input', function (event) {
                    this.value = event.target.value.replace(/[\uFF01-\uFF5E]/g, function(ch) {
                        return String.fromCharCode(ch.charCodeAt(0) - 0xFEE0);
                    }).replace(/\u3000/g, " ");

                    updateRegion(regionId, region, "");
                    region.val("");
                    city.val("");
                    updateStreet(street1, "");
                    updateStreet(street2, "");
                    $('#temp_street').val("");
                    street1.val("").removeAttr('disabled');
                    $('.street_2').val("").prop("disabled", true);
                    $('.street_3').val("").prop("disabled", true);
                    $('.zip-error').remove();
                    $('#zip-error').remove();

                    if (!this.value) {
                        $(this).after('<div class="zip-error" style="margin-top: 7px; color: #e02b27; font-size: 1.2rem">' + $t('Please enter your postal code.') + '</div>');
                    } else if (isNaN(this.value.replaceAll('-', ''))) {
                        $(this).after('<div class="zip-error" style="margin-top: 7px; color: #e02b27; font-size: 1.2rem">' + $t('Please enter in half-width numbers.') + '</div>');
                    } else if (this.value.replaceAll('-', '').length !== 7) {
                        $(this).after('<div class="zip-error" style="margin-top: 7px; color: #e02b27; font-size: 1.2rem">' + $t('Please enter %1 digits.').replace('%1', 7) + '</div>');
                    } else {
                        loadPostalCodeData(event.target.value).then(
                            function (data) {
                                updateRegion(regionId, region, data.region);
                                region.val(data.region);
                                city.val(data.city);
                                updateStreet(street1, data.street1);
                                updateStreet(street2, data.street2);
                                $('#temp_street').val(data.street1);

                                let field = $(event.target).closest('td');
                                let header = field.closest('tr').find('th');

                                field.removeClass('error-bg').css('background-color', '');
                                header.removeClass('error-bg').css('background-color', '');
                                street1.prop("disabled", true);
                                $('#region_id-error').remove();
                                $('#city-error').remove();

                                if (!data.street1) {
                                    data.street1 = '';
                                }

                                $('#candidateAddr').html('<div>'+`${data.region}${data.city}${data.street1}`+'</div>')
                                $('.street_2').removeAttr("disabled")
                                $('.street_3').removeAttr("disabled")
                            },
                            function () {
                                if (event.target.value.replaceAll('-', '').length === 7 && !isNaN(event.target.value.replaceAll('-', ''))) {
                                    $(event.target).after('<div class="zip-error" style="margin-top: 7px; color: #e02b27; font-size: 1.2rem">' + $t('Invalid postal code.') + '</div>');
                                }
                            }
                        )
                    }
                });
                $('#street_2').on('input', function (event) {
                    $('#street_1-error').remove();
                    $('#street_2-error').remove();
                    let field = $(event.target).closest('td');
                    let header = field.closest('tr').find('th');

                    field.removeClass('error-bg error-bg-1').css('background-color', '');
                    header.removeClass('error-bg error-bg-1').css('background-color', '');

                    if (!this.value) {
                        field.append('<div id="street_2-error" class="mage-error">' + $t('Please enter the address.') + '</div>');
                    }
                });
            }
        });
        return $.magentoJapanesePostalCode.addressUpdater;
    }
});
