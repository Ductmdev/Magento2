var config = {
    map: {
        '*': {
            'slick': 'Magento_PageBuilder/js/resource/slick/slick',
        }
    },
    shim: {
        'Magento_PageBuilder/js/resource/slick/slick': {
            deps: ['jquery']
        }
    }
};