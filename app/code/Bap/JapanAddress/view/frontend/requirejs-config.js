/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            postalCodeDataProvider: 'Bap_JapanAddress/js/service/madefor-postal-code-api'
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/form/element/post-code': {
                'Bap_JapanAddress/js/ui/form-postal-code-element': true
            }
        }
    }
};
