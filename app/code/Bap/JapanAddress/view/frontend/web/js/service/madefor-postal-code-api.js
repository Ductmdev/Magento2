define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    const API_ENDPOINT = 'https://madefor.github.io/postal-code-api/api/v1';
    let lastRequest = null;

    const getPostalCodeData = function (postalCode, lang) {
        const result = $.Deferred();

        if (lastRequest && lastRequest.readyState !== 4) {
            lastRequest.abort();
        }

        const matches = postalCode.match(/^(\d{3})-?(\d{4})$/);

        if (!matches) {
            result.reject(
                $t('Provided Zip/Postal Code seems to be invalid.') + ' ' +
                $t(' Example: ') + '123-4567; 1234567.'
            );
            return result.promise();
        }

        const apiUrl = `${API_ENDPOINT}/${matches[1]}/${matches[2]}.json`;

        lastRequest = $.ajax({
            url: apiUrl,
            dataType: 'json'
        })
            .done((apiResponse) => {
                const data = apiResponse?.data?.[0]?.[lang] ?? apiResponse?.data?.[0]?.ja;

                if (!data) {
                    result.reject($t('Provided Zip/Postal Code is unknown.'));
                    return;
                }

                result.resolve({
                    region: data.prefecture || null,
                    city: data.address1 || null,
                    street1: [data.address2, data.address3].filter(Boolean).join(' ') || null,
                    street2: data.address4 || null
                });
            })
            .fail((jqXHR, textStatus) => {
                if (textStatus === 'abort') return;
                const errorMessage = jqXHR.status === 404
                    ? $t('Provided Zip/Postal Code is unknown.')
                    : $t('Unable to verify provided Zip/Postal Code.');
                result.reject(errorMessage);
            });

        return result.promise();
    };

    return function (lang) {
        return (postalCode) => getPostalCodeData(postalCode, lang);
    };
});
