define(
    [
        'Magento_Ui/js/grid/columns/column',
        'jquery', 
        'uiRegistry'
    ],
    function (Column, $, uiRegistry) {
        return Column.extend({
            defaults: {
                bodyTmpl: 'ui/grid/cells/html',
                fieldClass: { 'data-grid-html-cell': true }
            },
            initialize: function () {
                this._super();
                this.initButtonEvent();
                
                return this;
            },
            getLabel: function (row) {
                return row[this.index + '_html']
            },
            initButtonEvent: function () {
                $(document).off('click', '.select-product-jancode').on('click', '.select-product-jancode', function () {
                    let productNameVal= $(this).data('name');
                    let jancodeVal = $(this).data('jan_code');

                    let modal = uiRegistry.get('index = select_product_modal');
                    if (modal) {
                        $('body').trigger('processStart');

                        setTimeout(function() {
                            let jancodeInput = uiRegistry.get('index = bap_jancode');
                            let productNameInput = uiRegistry.get('index = product_name');

                            if (jancodeInput) {
                                jancodeInput.value(jancodeVal);
                            }
                            if (productNameInput) {
                                productNameInput.value(productNameVal);
                            }
                            $('body').trigger('processStop');

                            modal.closeModal();
                        }, 700);
                    }
                });
            }
        });
    }
);