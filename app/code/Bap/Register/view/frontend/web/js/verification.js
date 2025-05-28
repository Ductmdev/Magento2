define([
    'jquery',
    'mage/mage'
], function ($) {
    'use strict';

    $.widget('verification.submit', {
        _create: function () {
            var formSelector = $(this.options.formSelector),
                inputSelector = $(this.options.inputSelector),
                errorContainerSelector = $(this.options.errorContainerSelector),
                urlVerify = this.options.urlVerify,
                urlTimeout = this.options.urlTimeout,
                verifyToken = $(this.options.verifyToken),
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
                            token: verifyToken.val()
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
            formSelector.mage('validation').find('input:text').attr('autocomplete', 'off');

            formSelector.on('submit', function (e) {
                e.preventDefault();

                if (localStorage.getItem("timeout") === "true") {
                    checkTimeout();
                    return;
                }

                var verifyCode = inputSelector.val();
                if (verifyCode.length !== 6 || isNaN(verifyCode)) {
                    errorContainerSelector.text('').hide();
                    inputSelector.val('');
                    return;
                }

                $.ajax({
                    url: urlVerify,
                    type: 'POST',
                    data: {
                        verify_code: verifyCode,
                        verify_token: verifyToken.val()
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.valid) {
                            errorContainerSelector.text('').hide();
                            window.location.href = response.redirect;
                        } else {
                            errorContainerSelector.text(response.message).show();
                            inputSelector.val("");
                        }
                    }
                });
            })
        }
    })

    return $.verification.submit;
})
