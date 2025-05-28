define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('confirm.submit', {
        _create: function () {
            var urlTimeout = this.options.urlTimeout,
                token = this.options.token,
                timeout;

            function startTimer() {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    localStorage.setItem("timeout", "true");
                    checkTimeout();
                }, 10 * 60 * 1000);
            }

            function checkTimeout() {
                if (localStorage.getItem("timeout") === "true") {
                    localStorage.clear();
                    $.ajax({
                        url: urlTimeout,
                        type: 'POST',
                        data: {
                            token: token
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.valid) {
                                window.location.href = response.redirect;
                            }
                        }
                    });
                }
            }

            startTimer();
            $(document).on("mousemove keypress click", startTimer);

            $('#confirm').on('submit', function () {
                localStorage.clear();
            })
        }
    })

    return $.confirm.submit;
})
