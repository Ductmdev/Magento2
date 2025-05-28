define(['jquery'], function ($) {
    'use strict';
    $(document).ready(function () {
        $('#search').on('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.stopPropagation();
            }
        });
    });
});
