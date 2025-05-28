define([
    'Magento_Ui/js/lib/validation/validator',
    'jquery',
    'underscore',
    'mage/translate'
], function (validator, $, _) {
    'use strict';

    var rules = [
        {
            id: 'phoneJP',

            handler: function (value) {
                return /^(?:\d{10}|\d{3}-\d{3}-\d{4}|\d{2}-\d{4}-\d{4}|\d{3}-\d{4}-\d{4})$/.test(value);
            },
            errorMessage: $.mage.__('Please enter your phone number in the correct format.')
        },
        {
            id: 'email-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your email address.')
        },
        {
            id: 'email-validate',

            handler: function (value) {
                return $.mage.isEmptyNoTrim(value) || /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(value);
            },
            errorMessage: $.mage.__('There is an error in your email address.')
        },
        {
            id: 'lastname-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your name (last name).')
        },
        {
            id: 'firstname-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your name (first name).')
        },
        {
            id: 'lastnamekana-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your furigana (last name).')
        },
        {
            id: 'firstnamekana-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your furigana (first name).')
        },
        {
            id: 'validate-lastnamekana',

            handler: function (value) {
                return /^([\u3040-\u309F|\u30FB-\u30FC|\u30A1-\u30FC])*$/.test(value);
            },
            errorMessage: $.mage.__('Please enter the furigana (last name) in katakana.')
        },
        {
            id: 'validate-firstnamekana',

            handler: function (value) {
                return /^([\u3040-\u309F|\u30FB-\u30FC|\u30A1-\u30FC])*$/.test(value);
            },
            errorMessage: $.mage.__('Please enter the furigana (first name) in katakana.')
        },
        {
            id: 'validate-lastnamekana-fullwidth',

            handler: function (value) {
                return /^([\u3040-\u309F|\u30FB-\u30FC|\u30A1-\u30FC])*$/.test(value);
            },
            errorMessage: $.mage.__('Please enter the furigana (last name) in katakana (full-width).')
        },
        {
            id: 'validate-firstnamekana-fullwidth',

            handler: function (value) {
                return /^([\u3040-\u309F|\u30FB-\u30FC|\u30A1-\u30FC])*$/.test(value);
            },
            errorMessage: $.mage.__('Please enter the furigana (first name) in katakana (full-width).')
        },
        {
            id: 'verify-code-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your verification number.')
        },
        {
            id: 'min-length-number',

            handler: function (value, element, params) {
                return value.length === params && !isNaN(value);
            },
            errorMessage: $.mage.__('Please enter the verification number using {0} half-width numbers.')
        },
        {
            id: 'gender-required',

            handler: function (v, elm, selector) {
                var name = elm.name.replace(/([\\"])/g, '\\$1'),
                    container = this.currentForm;

                selector = selector === true ? 'input[name="' + name + '"]:checked' : selector;

                return !!container.querySelectorAll(selector).length;
            },
            errorMessage: $.mage.__('Please select your gender.')
        },
        {
            id: 'dob-required',

            handler: function (value, element, params) {
                let year = $(params.year).val();
                let month = $(params.month).val();
                let day = $(params.day).val();

                return !(year === "" && month === "" && day === "");
            },
            errorMessage: $.mage.__('Please enter your date of birth.')
        },
        {
            id: 'valid-dob',

            handler: function (value, element, params) {
                let year = $(params.year).val();
                let month = $(params.month).val();
                let day = $(params.day).val();
                let missingCount = (!year ? 1 : 0) + (!month ? 1 : 0) + (!day ? 1 : 0);

                return missingCount === 0 || missingCount === 3;
            },
            errorMessage: $.mage.__('Please enter your date of birth correctly.')
        },
        {
            id: 'zip-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your postal code.')
        },
        {
            id: 'region-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please select a prefecture.')
        },
        {
            id: 'city-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter the city.')
        },
        {
            id: 'street-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter the address.')
        },
        {
            id: 'phone-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter the phone number.')
        },
        {
            id: 'password-required',

            handler: function (value) {
                return !$.mage.isEmpty(value);
            },
            errorMessage: $.mage.__('Please enter your password.')
        },
        {
            id: 'min-length-password',

            handler: function (value, element, params) {
                return value.length >= params;
            },
            errorMessage: $.mage.__('Please enter your password using a combination of half-width alphanumeric characters and at least {0} digits.')
        },
        {
            id: 'alphanum-only',

            handler: function (v) {
                return $.mage.isEmptyNoTrim(v) || /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/.test(v);
            },
            errorMessage: $.mage.__('Please specify a password that includes alphanumeric characters.')
        },
        {
            id: 'sameAsPassword',

            handler: function (value, element, param) {
                return value === $(param).val();
            },
            errorMessage: $.mage.__('Password does not match')
        }
    ];

    return function (target) {
        _.each(rules, function (rule) {
            validator.addRule(
                rule.id,
                rule.handler,
                $.mage.__(rule.errorMessage)
            );
            $.validator.addMethod(
                rule.id,
                rule.handler,
                $.mage.__(rule.errorMessage)
            );
        });

        return target;
    };
});
