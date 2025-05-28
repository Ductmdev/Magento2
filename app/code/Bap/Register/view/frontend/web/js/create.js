define([
    'jquery',
    'mage/translate',
    'mage/mage'
], function($, $t){
    'use strict';

    $.widget('create.submit', {
        _create: function () {
            var dataForm = $('#form-validate'),
                urlTimeout = this.options.urlTimeout,
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
                            token: $('#token').val()
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

            dataForm.mage('validation', {
                errorPlacement: function (error, element) {
                    var td = element.closest('td');
                    var tr = element.closest('tr');
                    var th = tr.find('th');
                    var span = th.find('span');

                    if (element.hasClass('street_2')) {
                        td.addClass('error-bg-1');
                        th.addClass('error-bg-1');
                    }

                    var zipElement = $(element).parents('.zip')

                    if (zipElement.length) {
                        error.insertAfter(zipElement);
                    } else {
                        td.append(error);
                    }

                    td.addClass('error-bg');
                    th.addClass('error-bg');
                    span.removeClass('bcs_label_gray').addClass('bcs_label');
                },
                success: function (label, element) {
                    var td = $(element).closest('td');
                    var tr = $(element).closest('tr');
                    var th = tr.find('th');
                    var span = th.find('span');

                    if ($(element).hasClass('street_2')) {
                        td.removeClass('error-bg-1');
                        th.removeClass('error-bg-1');
                    }

                    td.removeClass('error-bg');
                    th.removeClass('error-bg');
                    span.removeClass('bcs_label').addClass('bcs_label_gray');
                    label.remove();
                }
            }).find('input:text').attr('autocomplete', 'off');

            function resetStyles(fields) {
                fields.forEach(field => {
                    let fieldTd = $(field).closest('td');
                    let fieldTh = fieldTd.closest('tr').find('th');
                    let fieldLabel = fieldTh.find('span');
                    fieldTd.css('background-color', '');
                    fieldTh.css('background-color', '');
                    fieldLabel.css('background', '');
                });
            }

            function showError(fields) {
                fields.forEach(field => {
                    let fieldTd = $(field).closest('td');
                    let fieldTh = fieldTd.closest('tr').find('th');
                    let fieldLabel = fieldTh.find('span');
                    fieldTd.css('background-color', '#fff6f7');
                    fieldTh.css('background-color', '#ffeaec');
                    fieldLabel.css('background', '#e60012');
                });
                $('.bcs_attentionPanel').show();
            }

            function validateKana(name) {
                return /^([\u30A1-\u30FC])*$/.test(name);
            }

            function validateForm(e) {
                $('.bcs_attentionPanel').hide();
                $('.zip-error').remove();
                let errors = false;

                let lastname = $('#lastname').val().trim();
                let firstname = $('#firstname').val().trim();
                resetStyles(['#lastname']);
                if (!lastname || !firstname) {
                    showError(['#lastname']);
                    errors = true;
                }

                let lastnamekana = $('#lastnamekana').val().trim();
                let firstnamekana = $('#firstnamekana').val().trim();
                resetStyles(['#lastnamekana']);
                if (!lastnamekana || !firstnamekana || !validateKana(lastnamekana) || !validateKana(firstnamekana)) {
                    showError(['#lastnamekana']);
                    errors = true;
                }

                if (!$("input[name='gender']:checked").val()) {
                    $('.bcs_attentionPanel').show();
                    errors = true;
                }

                resetStyles(['#BIRTH_DT_YEAR']);
                if (!$('#BIRTH_DT_YEAR').val() || !$('#BIRTH_DT_MONTH').val() || !$('#BIRTH_DT_DAY').val()) {
                    showError(['#BIRTH_DT_YEAR']);
                    $('.bcs_attentionPanel').show();
                    errors = true;
                }

                let tel = $('#telephone').val().trim();
                resetStyles(['#telephone']);
                if (!tel || isNaN(tel)) {
                    showError(['#telephone']);
                    $('.bcs_attentionPanel').show();
                    errors = true;
                }

                let password = $('#password').val().trim();
                let confirm_password = $('#confirm_password').val().trim();
                resetStyles(['#password']);
                if (!password || !/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/.test(password)) {
                    showError(['#password']);
                    $('.bcs_attentionPanel').show();
                    errors = true;
                }

                resetStyles(['#confirm_password']);
                if (!confirm_password || password !== confirm_password) {
                    showError(['#confirm_password']);
                    $('.bcs_attentionPanel').show();
                    errors = true;
                }

                let street_2 = $('#street_2').val().trim();
                resetStyles(['#street_2']);
                if (!street_2) {
                    showError(['#street_2']);
                    errors = true;
                }

                let zip = $('#zip').val().trim();
                let region = $('#region').val().trim();
                let city = $('#city').val().trim();
                resetStyles(['.zip']);
                if (zip && zip.length < 7) {
                    $('.zip').after('<div class="zip-error" style="color: #e02b27; font-size: 1.2rem">' + $t('Please enter the postal code using %1 half-width digits.').replace('%1', 7) + '</div>');
                    showError(['.zip']);
                    errors = true;
                } else if (zip && (!region || !city)) {
                    $('.zip').after('<div class="zip-error" style="color: #e02b27; font-size: 1.2rem">' + $t('The postal code you entered is incorrect please enter a valid postal code.') + '</div>');
                    showError(['.zip']);
                    errors = true;
                }

                if (errors) {
                    e.preventDefault();
                }
            }

            dataForm.on('submit', validateForm);

            $("#confirmBack").click(function(event) {
                event.preventDefault();
                $("#cboxOverlay").show();
                $("#colorbox").show();
            });

            $("#cboxOverlay").click(function() {
                $("#cboxOverlay").hide();
                $("#colorbox").hide();
            });

            $(".back_proceed").click(function(event) {
                event.preventDefault();
                $("#cboxOverlay").hide();
                $("#colorbox").hide();
            });

            $('#togglePassword').on('change', function () {
                $('#password').attr('type', this.checked ? 'text' : 'password');
            })

            $('#password').on('input', function () {
                let remainCount = $('#remain_BIC_PW');
                remainCount.text(100 - this.value.length);

                $('#tooltips_BIC_PW').css('display', this.value.length >= 7 ? 'none' : 'inline-block');
            })

            $('#lastname').on('input', function () {
                $('#lastname-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');
                let lastname = this.value.trim();

                if (!lastname) {
                    fieldTd.find('.fullName').after('<div id="lastname-error" class="mage-error">' + $t('Please enter your name (last name).') + '</div>');
                }

                if (lastname && $('#firstname').val().trim()) {
                    fieldTd.removeClass('error-bg').css('background-color', '');
                    fieldTh.removeClass('error-bg').css('background-color', '');
                }
            })

            $('#firstname').on('input', function () {
                $('#firstname-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');
                let firstname = this.value.trim();

                if (!firstname) {
                    fieldTd.append('<div id="firstname-error" class="mage-error">' + $t('Please enter your name (first name).') + '</div>');
                }

                if (firstname && $('#lastname').val().trim()) {
                    fieldTd.removeClass('error-bg').css('background-color', '');
                    fieldTh.removeClass('error-bg').css('background-color', '');
                }
            })

            $('#lastnamekana').on('input', function () {
                $('#lastnamekana-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');
                let lastnamekana = this.value.trim();
                let firstnamekana = $('#firstnamekana').val().trim();

                if (!lastnamekana) {
                    $('#lastnamekana-error').remove();
                    fieldTd.find('.fullName').after('<div id="lastnamekana-error" class="mage-error">' + $t('Please enter your furigana (last name).') + '</div>');
                } else if (!validateKana(lastnamekana)) {
                    $('#lastnamekana-error').remove();
                    fieldTd.find('.fullName').after('<div id="lastnamekana-error" class="mage-error">' + $t('Please enter the furigana (last name) in katakana (full-width).') + '</div>');
                }

                if (lastnamekana && validateKana(lastnamekana) && firstnamekana && validateKana(firstnamekana)) {
                    fieldTd.removeClass('error-bg').css('background-color', '');
                    fieldTh.removeClass('error-bg').css('background-color', '');
                }
            })

            $('#firstnamekana').on('input', function () {
                $('#firstnamekana-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');
                let firstnamekana = this.value.trim();
                let lastnamekana = $('#lastnamekana').val().trim();

                if (!firstnamekana) {
                    $('#firstnamekana-error').remove();
                    fieldTd.append('<div id="firstnamekana-error" class="mage-error">' + $t('Please enter your furigana (first name).') + '</div>');
                } else if (!validateKana(firstnamekana)) {
                    $('#firstnamekana-error').remove();
                    fieldTd.append('<div id="firstnamekana-error" class="mage-error">' + St('Please enter the furigana (first name) in katakana (full-width).') + '</div>');
                }

                if (firstnamekana && validateKana(firstnamekana) && lastnamekana && validateKana(lastnamekana)) {
                    fieldTd.removeClass('error-bg').css('background-color', '');
                    fieldTh.removeClass('error-bg').css('background-color', '');
                }
            })

            $('input[name="gender"]').on('change', function () {
                $('#gender-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');

                fieldTd.removeClass('error-bg').css('background-color', '');
                fieldTh.removeClass('error-bg').css('background-color', '');
            });

            $('#BIRTH_DT_YEAR, #BIRTH_DT_MONTH, #BIRTH_DT_DAY').on('change', function () {
                let year = $('#BIRTH_DT_YEAR').val();
                let month = $('#BIRTH_DT_MONTH').val();
                let day = $('#BIRTH_DT_DAY').val();
                let td = $('#dob').closest('td');
                let th = $('#birthDate');
                var span = th.find('span');

                $('#BIRTH_DT_YEAR-error').remove();
                td.css('background-color', '');
                th.css('background-color', '');

                let missingCount = (!year ? 1 : 0) + (!month ? 1 : 0) + (!day ? 1 : 0);

                if (missingCount === 3) {
                    td.append('<div id="BIRTH_DT_YEAR-error" class="mage-error">' + $t('Please enter your date of birth.') + '</div>');
                } else if (missingCount > 0) {
                    td.append('<div id="BIRTH_DT_YEAR-error" class="mage-error">' + $t('Please enter your date of birth correctly.') + '</div>');
                } else {
                    td.removeClass('error-bg').css('background-color', '');
                    th.removeClass('error-bg').css('background-color', '');
                }
            });

            $('#telephone').on('input', function () {
                $('#telephone-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');
                let tel = this.value.trim();

                if (!tel) {
                    fieldTd.append('<div id="telephone-error" class="mage-error">' + $t('Please enter the phone number.') + '</div>');
                } else if (isNaN(tel)) {
                    fieldTd.append('<div id="telephone-error" class="mage-error">' + $t('Please enter in half-width numbers.') + '</div>');
                } else {
                    fieldTd.removeClass('error-bg').css('background-color', '');
                    fieldTh.removeClass('error-bg').css('background-color', '');
                }
            });

            $('#password').on('input', function () {
                $('#password-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');
                let password = this.value.trim();

                if (!password) {
                    fieldTd.append('<div id="password-error" class="mage-error">' + $t('Please enter your password.') + '</div>');
                } else if (!/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/.test(password)) {
                    fieldTd.append('<div id="password-error" class="mage-error">' + $t('Please specify a password that includes alphanumeric characters.') + '</div>');
                } else {
                    fieldTd.removeClass('error-bg').css('background-color', '');
                    fieldTh.removeClass('error-bg').css('background-color', '');
                }
            })

            $('#confirm_password').on('input', function () {
                $('#confirm_password-error').remove();
                let fieldTd = $(this).closest('td');
                let fieldTh = fieldTd.closest('tr').find('th');
                let confirm_password = this.value.trim();

                if (!confirm_password) {
                    fieldTd.append('<div id="confirm_password-error" class="mage-error">' + $t('Please enter your password.') + '</div>');
                } else {
                    fieldTd.removeClass('error-bg').css('background-color', '');
                    fieldTh.removeClass('error-bg').css('background-color', '');
                }
            })
        }
    })

    return $.create.submit;
});
