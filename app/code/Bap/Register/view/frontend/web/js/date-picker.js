define([
    'jquery'
], function ($) {
    'use strict';

    return function () {
        $(document).ready(function () {
            function updateDays() {
                var daySelect = $('#BIRTH_DT_DAY');
                var month = parseInt($('#BIRTH_DT_MONTH').val(), 10);
                var year = parseInt($('#BIRTH_DT_YEAR').val(), 10);
                var daysInMonth = 31;

                if (month === 4 || month === 6 || month === 9 || month === 11) {
                    daysInMonth = 30;
                } else if (month === 2) {
                    if ((year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0)) {
                        daysInMonth = 29;
                    } else {
                        daysInMonth = 28;
                    }
                }

                var oldDay = daySelect.val();
                daySelect.empty().append('<option value="">' + '--' + '</option>');

                for (var i = 1; i <= daysInMonth; i++) {
                    daySelect.append('<option value="' + i + '">' + i + '</option>');
                }

                if (oldDay && oldDay <= daysInMonth) {
                    daySelect.val(oldDay);
                }
            }

            $('#BIRTH_DT_MONTH, #BIRTH_DT_YEAR').change(updateDays);
        });
    }
})
