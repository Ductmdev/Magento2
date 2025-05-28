define([
    'jquery',
    'mage/mage'
], function($){
    'use strict';

    $.widget('register.submit', {
        _create: function () {
            var urlRegister = this.options.urlRegister,
                dataForm = $('#form-validate');

            $(document).ready(function () {
                $('#email').val(localStorage.getItem('email') || "");
                $('#lastname').val(localStorage.getItem('lastname') || "");
                $('#firstname').val(localStorage.getItem('firstname') || "");
                $('#lastnamekana').val(localStorage.getItem('lastnamekana') || "");
                $('#firstnamekana').val(localStorage.getItem('firstnamekana') || "");
            })

            dataForm.mage('validation', {
                errorPlacement: function (error, element) {
                    var td = element.closest('td');
                    var tr = element.closest('tr');
                    var th = tr.find('th');

                    if (td.length) {
                        td.append(error);
                        td.addClass('error-bg');
                    }

                    if (th.length) {
                        th.addClass('error-bg');
                    }
                },
                success: function (label, element) {
                    var td = $(element).closest('td');
                    var tr = $(element).closest('tr');
                    var th = tr.find('th');

                    td.removeClass('error-bg');
                    th.removeClass('error-bg');
                    label.remove();
                }
            }).find('input:text').attr('autocomplete', 'off');

            function resetStyles(fields) {
                fields.forEach(field => {
                    let fieldTd = $(field).closest('td');
                    let fieldTh = fieldTd.closest('tr').find('th');
                    fieldTd.css('background-color', '');
                    fieldTh.css('background-color', '');
                });
            }

            function showError(fields) {
                fields.forEach(field => {
                    let fieldTd = $(field).closest('td');
                    let fieldTh = fieldTd.closest('tr').find('th');
                    fieldTd.css('background-color', '#fff6f7');
                    fieldTh.css('background-color', '#ffeaec');
                });
            }

            function validateKana(name) {
                return /^([\u30A1-\u30FC])*$/.test(name);
            }

            function validateEmail(email) {
                return /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(email);
            }

            function validateForm(e) {
                $('.email-disable').hide();
                $('.email-exist').hide();
                let error = false;
                let email = $('#email').val().trim();

                resetStyles(['#email']);
                if (!email || !validateEmail(email)) {
                    showError(['#email']);
                    error = true;
                }

                let lastname = $('#lastname').val().trim();
                let firstname = $('#firstname').val().trim();
                resetStyles(['#lastname']);
                if (!lastname || !firstname) {
                    showError(['#lastname']);
                    error = true;
                }

                let lastnamekana = $('#lastnamekana').val().trim();
                let firstnamekana = $('#firstnamekana').val().trim();
                resetStyles(['#lastnamekana']);
                if (!lastnamekana || !firstnamekana || !validateKana(lastnamekana) || !validateKana(firstnamekana)) {
                    showError(['#lastnamekana']);
                    error = true;
                }

                localStorage.setItem('email', email);
                localStorage.setItem('lastname', lastname);
                localStorage.setItem('firstname', firstname);
                localStorage.setItem('lastnamekana', lastnamekana);
                localStorage.setItem('firstnamekana', firstnamekana);

                if (!error) {
                    e.preventDefault();
                    $.ajax({
                        url: urlRegister,
                        type: 'POST',
                        data: {
                        email: email,
                            lastname: lastname,
                            firstname: firstname,
                            lastnamekana: lastnamekana,
                            firstnamekana: firstnamekana
                    },
                    dataType: 'json',
                        success: function (response) {
                        if (response.code === 1) {
                            window.location.href = response.redirect;
                        } else if (response.code === 2) {
                            $('.email-disable').text(response.message).show();
                            showError(['#email']);
                        } else {
                            $('.email-exist').show();
                        }
                    }
                });
                }
            }

            dataForm.on('submit', validateForm);
        }
    })

    return $.register.submit;
});
