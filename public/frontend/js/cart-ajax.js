// ==========================================
// CART PAGE AJAX LOGIC (HNOWW Pattern)
// ==========================================

// HEADER MINI CART / WISHLIST - LIVE REFRESH HELPERS
function refreshMiniCart() {
    if (window.appRoutes && window.appRoutes.miniCart) {
        $.get(window.appRoutes.miniCart, function(html) {
            $('#mini-cart-box').html(html);
        });
    }
}

function refreshMiniWishlist() {
    if (window.appRoutes && window.appRoutes.miniWishlist) {
        $.get(window.appRoutes.miniWishlist, function(html) {
            $('#mini-wishlist-box').html(html);
        });
    }
}

// QTY INCREMENT / DECREMENT (AJAX)
$(document).on('click', '.inc_btn', function () {
    let row = $(this).closest('.increment_decrement');
    let qtyInput = row.find('.qty_input');
    let qty = parseInt(qtyInput.val());
    let stock = parseInt(row.data('stock'));
    let cartId = row.data('cart-id');

    if (qty < stock) {
        qty++;
        updateCartQty(cartId, qty, qtyInput, row);
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Out of Stock',
            text: 'You cannot add more than available stock (' + stock + ')',
            confirmButtonColor: '#F7941D',
        });
    }
});

$(document).on('click', '.dec_btn', function () {
    let row = $(this).closest('.increment_decrement');
    let qtyInput = row.find('.qty_input');
    let qty = parseInt(qtyInput.val());
    let cartId = row.data('cart-id');

    if (qty > 1) {
        qty--;
        updateCartQty(cartId, qty, qtyInput, row);
    }
});

function updateCartQty(cartId, qty, qtyInput, row) {
    $.ajax({
        url: window.appRoutes.cartUpdate,
        type: "POST",
        data: {
            _token: window.appCsrfToken,
            quant: { 0: qty },
            qty_id: [cartId]
        },
        success: function (response) {
            if (response.status) {
                qtyInput.val(qty);
                recalculateRowTotal(row, qty);
                updateCartTotalsFromServer(response);
                updateHeaderCartCount();
                refreshMiniCart();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: response.message,
                    confirmButtonColor: '#F7941D',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong!',
            });
        }
    });
}

function updateCartTotalsFromServer(response) {
    if (response.subtotal !== undefined) {
        $('#cart-subtotal').text('₹' + parseFloat(response.subtotal).toFixed(2));
    }
    if (response.gst_total !== undefined) {
        if (response.gst_total > 0) {
            $('#gst_amount span').text('₹' + parseFloat(response.gst_total).toFixed(2));
            $('#gst_amount').show();
        } else {
            $('#gst_amount').hide();
        }
    }
    if (response.grand_total !== undefined) {
        $('#you-pay').text('₹' + parseFloat(response.grand_total).toFixed(2));
    }
}

function recalculateRowTotal(row, qty) {
    let tr = row.closest('tr');
    let price = parseFloat(tr.find('.unit-price').data('price'));
    let rowTotal = qty * price;
    tr.find('.row-total').text('₹' + rowTotal.toFixed(2));
}

function recalculateCartTotals() {
    let subtotal = 0;
    $('.cart-item-row').each(function () {
        let qty = parseInt($(this).find('.qty_input').val());
        let price = parseFloat($(this).find('.unit-price').data('price'));
        subtotal += qty * price;
    });
    $('#cart-subtotal').text('₹' + subtotal.toFixed(2));
    $('#you-pay').text('₹' + subtotal.toFixed(2));
}

// DELETE CART ITEM (AJAX)
$(document).on('click', '.delete-cart-item', function () {
    let cartId = $(this).data('id');
    Swal.fire({
        icon: 'warning',
        title: 'Remove this item from cart?',
        text: 'This item will be removed from your cart.',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'No, keep it',
        confirmButtonColor: '#F7941D',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.appRoutes.cartDeleteBase + '/' + cartId,
                type: "GET",
                success: function (response) {
                    if (response.status) {
                        $('#cart-row-' + cartId).remove();
                        recalculateCartTotals();
                        updateHeaderCartCount();
                        refreshMiniCart();

                        if ($('#cart_item_list .cart-item-row').length === 0) {
                            let emptyRow = `<tr class="empty-cart-row"><td class="text-center" colspan="7">
                                There are no any carts available. <a href="${window.appData.productListUrl}" style="color:blue;">Continue shopping</a>
                            </td></tr>`;
                            $('#cart_item_list').html(emptyRow);
                            $('#calculation-section').remove();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                }
            });
        }
    });
});

// ==========================================
// CHECKOUT AUTH MODAL LOGIC (HNOWW Pattern)
// ==========================================
$(document).ready(function() {
    var isRegistered = false;
    var userEmail = '';

    var forgotOtpTimer = null;
    var forgotOtpSeconds = 60;

    // ---- FIELD-LEVEL VALIDATION HELPERS ----
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showFieldError(fieldId, message) {
        if (!message) return;
        $('#error_' + fieldId).text(message).removeClass('d-none');
    }

    function clearFieldError(fieldId) {
        $('#error_' + fieldId).addClass('d-none').text('');
    }

    function clearAllFieldErrors() {
        $('.field-error').addClass('d-none').text('');
    }

    function getAjaxErrorMessage(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }
        return 'Something went wrong. Please try again.';
    }

    function startForgotOtpTimer() {
        clearInterval(forgotOtpTimer);
        forgotOtpSeconds = 60;
        $('#btn-resend-forgot-otp').prop('disabled', true);
        $('#forgot-otp-timer').text('(60s)');
        forgotOtpTimer = setInterval(function() {
            forgotOtpSeconds--;
            $('#forgot-otp-timer').text('(' + forgotOtpSeconds + 's)');
            if (forgotOtpSeconds <= 0) {
                clearInterval(forgotOtpTimer);
                forgotOtpTimer = null;
                $('#forgot-otp-timer').text('');
                $('#btn-resend-forgot-otp').prop('disabled', false);
            }
        }, 1000);
    }

    // ---- STEP 1: EMAIL ----
    $('#btn-email-next').click(function() {
        clearAllFieldErrors();
        var email = $('#checkout_email').val().trim();

        if (!email) {
            showFieldError('checkout_email', 'Please enter your email address.');
            return;
        }
        if (!isValidEmail(email)) {
            showFieldError('checkout_email', 'Please enter a valid email address.');
            return;
        }

        $('#btn-email-next').prop('disabled', true).text('Checking...');

        $.ajax({
            url: window.appRoutes.checkoutCheckEmail,
            type: "POST",
            data: {
                _token: window.appCsrfToken,
                email: email
            },
            success: function(response) {
                $('#btn-email-next').prop('disabled', false).text('Continue');
                if (response.success) {
                    userEmail = email;
                    if (response.registered) {
                        isRegistered = true;
                        $('#checkoutAuthTitle').text('Welcome Back');
                        $('#step-email').addClass('d-none');
                        $('#step-login').removeClass('d-none');
                    } else {
                        isRegistered = false;
                        $('#checkoutAuthTitle').text('Create Account');
                        $('#step-email').addClass('d-none');
                        $('#step-register').removeClass('d-none');
                        $('#checkout_register_email').val(email);
                    }
                } else {
                    showFieldError('checkout_email', response.message);
                }
            },
            error: function(xhr) {
                $('#btn-email-next').prop('disabled', false).text('Continue');
                showFieldError('checkout_email', getAjaxErrorMessage(xhr));
            }
        });

    });

     $('#btn-goto-signup').click(function() {
        clearAllFieldErrors();
        var email = $('#checkout_email').val().trim();
        isRegistered = false;
        userEmail = email;
        $('#checkoutAuthTitle').text('Create Account');
        $('#step-email').addClass('d-none');
        $('#step-register').removeClass('d-none');
        $('#checkout_register_email').val(email);
    });

    $('#btn-login-back, #btn-register-back').click(function() {
        clearAllFieldErrors();
        $('#checkoutAuthTitle').text('Login to Checkout');
        $('.auth-step').addClass('d-none');
        $('#step-email').removeClass('d-none');
        $('#checkout_password').val('');
        $('#checkout_name').val('');
        $('#checkout_register_email').val('');
        $('#checkout_reg_password').val('');
        $('#checkout_reg_password_confirmation').val('');
    });

    // ---- STEP 2: LOGIN / REGISTER SUBMIT ----
    $('#checkout-auth-form').submit(function(e) {
        e.preventDefault();
        clearAllFieldErrors();
        var hasError = false;

        if (isRegistered) {
            var password = $('#checkout_password').val();
            if (!password) {
                showFieldError('checkout_password', 'Please enter your password.');
                hasError = true;
            }
        } else {
            var name = $('#checkout_name').val().trim();
            var regEmail = $('#checkout_register_email').val().trim();
            var regPassword = $('#checkout_reg_password').val();
            var regConfirm = $('#checkout_reg_password_confirmation').val();

            if (!name) {
                showFieldError('checkout_name', 'Please enter your name.');
                hasError = true;
            }
            if (!regEmail) {
                showFieldError('checkout_register_email', 'Please enter your email address.');
                hasError = true;
            } else if (!isValidEmail(regEmail)) {
                showFieldError('checkout_register_email', 'Please enter a valid email address.');
                hasError = true;
            }
            if (!regPassword) {
                showFieldError('checkout_reg_password', 'Please enter a password.');
                hasError = true;
            } else if (regPassword.length < 6) {
                showFieldError('checkout_reg_password', 'Password must be at least 6 characters.');
                hasError = true;
            }
            if (!regConfirm) {
                showFieldError('checkout_reg_password_confirmation', 'Please confirm your password.');
                hasError = true;
            } else if (regPassword && regConfirm !== regPassword) {
                showFieldError('checkout_reg_password_confirmation', 'Passwords do not match.');
                hasError = true;
            }
        }

        if (hasError) {
            return;
        }

        var submitBtn = isRegistered ? $('#btn-login-submit') : $('#btn-register-submit');
        var originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Processing...');

        var url = isRegistered ? window.appRoutes.checkoutLogin : window.appRoutes.checkoutRegister;

        var formData = {
            _token: window.appCsrfToken,
            email: isRegistered ? userEmail : $('#checkout_register_email').val().trim()
        };

        if (isRegistered) {
            formData.password = $('#checkout_password').val();
        } else {
            formData.name = $('#checkout_name').val().trim();
            formData.reg_password = $('#checkout_reg_password').val();
            formData.reg_password_confirmation = $('#checkout_reg_password_confirmation').val();
        }

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect_url;
                } else {
                    submitBtn.prop('disabled', false).text(originalBtnText);
                    showFieldError(isRegistered ? 'checkout_password' : 'checkout_register_email', response.message);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text(originalBtnText);
                showFieldError(isRegistered ? 'checkout_password' : 'checkout_register_email', getAjaxErrorMessage(xhr));
            }
        });
    });

    // ==========================================
    // CHECKOUT FORGOT PASSWORD - OTP FLOW (DB based)
    // ==========================================
    $('#btn-forgot-password').click(function() {
        clearAllFieldErrors();
        if (!userEmail) {
            showFieldError('checkout_password', 'Email address is missing. Please go back and try again.');
            return;
        }
        var btn = $(this);
        var originalText = btn.text();
        btn.prop('disabled', true).text('Sending...');

        $.ajax({
            url: window.appRoutes.checkoutForgotSendOtp,
            type: "POST",
            data: {
                _token: window.appCsrfToken,
                email: userEmail
            },
            success: function(response) {
                if (response.success) {
                    $('#checkoutAuthTitle').text('Verify OTP');
                    $('#forgot-password-email').text(userEmail);
                    $('.auth-step').addClass('d-none');
                    $('#step-forgot-password').removeClass('d-none');
                    $('#checkout_forgot_otp').val('');
                    startForgotOtpTimer();
                } else {
                    showFieldError('checkout_password', response.message);
                }
            },
            error: function(xhr) {
                showFieldError('checkout_password', getAjaxErrorMessage(xhr));
            },
            complete: function() {
                btn.prop('disabled', false).text(originalText);
            }
        });
    });

    $('#btn-verify-forgot-otp').click(function() {
        clearFieldError('checkout_forgot_otp');
        var otp = $('#checkout_forgot_otp').val().trim();

        if (!otp) {
            showFieldError('checkout_forgot_otp', 'Please enter the OTP.');
            return;
        }
        if (!/^\d{6}$/.test(otp)) {
            showFieldError('checkout_forgot_otp', 'Please enter a valid 6-digit OTP.');
            return;
        }

        var btn = $(this);
        var originalText = btn.text();
        btn.prop('disabled', true).text('Verifying...');

        $.ajax({
            url: window.appRoutes.checkoutForgotVerifyOtp,
            type: "POST",
            data: {
                _token: window.appCsrfToken,
                email: userEmail,
                otp: otp
            },
            success: function(response) {
                if (response.success) {
                    $('#checkoutAuthTitle').text('Reset Password');
                    $('.auth-step').addClass('d-none');
                    $('#step-reset-password').removeClass('d-none');
                    $('#checkout_forgot_password').val('');
                    $('#checkout_forgot_password_confirmation').val('');
                } else {
                    showFieldError('checkout_forgot_otp', response.message);
                }
            },
            error: function(xhr) {
                showFieldError('checkout_forgot_otp', getAjaxErrorMessage(xhr));
            },
            complete: function() {
                btn.prop('disabled', false).text(originalText);
            }
        });
    });

    $('#btn-resend-forgot-otp').click(function() {
        clearFieldError('checkout_forgot_otp');
        if (!userEmail) {
            showFieldError('checkout_forgot_otp', 'Email address is missing. Please go back and try again.');
            return;
        }
        var btn = $(this);
        btn.prop('disabled', true).text('Sending...');

        $.ajax({
            url: window.appRoutes.checkoutForgotSendOtp,
            type: "POST",
            data: {
                _token: window.appCsrfToken,
                email: userEmail
            },
            success: function(response) {
                if (response.success) {
                    $('#checkout_forgot_otp').val('');
                    btn.html('Resend OTP <span id="forgot-otp-timer">(60s)</span>');
                    startForgotOtpTimer();
                } else {
                    btn.prop('disabled', false).html('Resend OTP');
                    showFieldError('checkout_forgot_otp', response.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('Resend OTP');
                showFieldError('checkout_forgot_otp', getAjaxErrorMessage(xhr));
            }
        });
    });

    $('#btn-reset-forgot-password').click(function() {
        clearFieldError('checkout_forgot_password');
        clearFieldError('checkout_forgot_password_confirmation');
        var password = $('#checkout_forgot_password').val();
        var confirmation = $('#checkout_forgot_password_confirmation').val();
        var hasError = false;

        if (!password) {
            showFieldError('checkout_forgot_password', 'Please enter a new password.');
            hasError = true;
        } else if (password.length < 6) {
            showFieldError('checkout_forgot_password', 'Password must be at least 6 characters.');
            hasError = true;
        }

        if (!confirmation) {
            showFieldError('checkout_forgot_password_confirmation', 'Please confirm your new password.');
            hasError = true;
        } else if (password && confirmation !== password) {
            showFieldError('checkout_forgot_password_confirmation', 'Passwords do not match.');
            hasError = true;
        }

        if (hasError) {
            return;
        }

        var btn = $(this);
        var originalText = btn.text();
        btn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: window.appRoutes.checkoutForgotReset,
            type: "POST",
            data: {
                _token: window.appCsrfToken,
                email: userEmail,
                password: password,
                password_confirmation: confirmation
            },
            success: function(response) {
                if (response.success) {
                    // Password reset ho gaya - wapas login step par le jao (continue checkout)
                    $('#checkoutAuthTitle').text('Welcome Back');
                    $('.auth-step').addClass('d-none');
                    $('#step-login').removeClass('d-none');
                    $('#checkout_password').val('');
                    clearAllFieldErrors();
                } else {
                    showFieldError('checkout_forgot_password_confirmation', response.message);
                }
            },
            error: function(xhr) {
                showFieldError('checkout_forgot_password_confirmation', getAjaxErrorMessage(xhr));
            },
            complete: function() {
                btn.prop('disabled', false).text(originalText);
            }
        });
    });

    $('#btn-forgot-back').click(function() {
        clearAllFieldErrors();
        clearInterval(forgotOtpTimer);
        $('#checkoutAuthTitle').text('Welcome Back');
        $('.auth-step').addClass('d-none');
        $('#step-login').removeClass('d-none');
        $('#checkout_forgot_otp').val('');
    });

    $('#btn-reset-password-back').click(function() {
        clearAllFieldErrors();
        $('#checkoutAuthTitle').text('Verify OTP');
        $('.auth-step').addClass('d-none');
        $('#step-forgot-password').removeClass('d-none');
        $('#checkout_forgot_password').val('');
        $('#checkout_forgot_password_confirmation').val('');
    });

    $('#checkoutAuthModal').on('hidden.bs.modal', function () {
        clearAllFieldErrors();
        clearInterval(forgotOtpTimer);
        forgotOtpTimer = null;
        forgotOtpSeconds = 60;
        $('#btn-resend-forgot-otp').prop('disabled', true).html('Resend OTP <span id="forgot-otp-timer">(60s)</span>');

        $('#checkoutAuthTitle').text('Login to Checkout');
        $('.auth-step').addClass('d-none');
        $('#step-email').removeClass('d-none');
        $('#checkout_email').val('');
        $('#checkout_password').val('');
        $('#checkout_name').val('');
        $('#checkout_register_email').val('');
        $('#checkout_reg_password').val('');
        $('#checkout_reg_password_confirmation').val('');
        $('#checkout_forgot_otp').val('');
        $('#checkout_forgot_password').val('');
        $('#checkout_forgot_password_confirmation').val('');
        isRegistered = false;
        userEmail = '';
    });
});

// Global scope me — updateCartQty() aur delete handler dono use kar sakein
function updateHeaderCartCount() {
    let totalQty = 0;
    $('.cart-item-row').each(function () {
        totalQty += parseInt($(this).find('.qty_input').val()) || 0;
    });
    $('.total-count').text(totalQty);
}

// FALLBACK: Manual modal open/close (agar Bootstrap JS ka data-toggle kaam na kare)
$(document).on('click', '[data-target="#checkoutAuthModal"]', function (e) {
    if ($(this).attr('href') === 'javascript:void(0);' || $(this).attr('data-toggle') === 'modal') {
        e.preventDefault();
        $('#checkoutAuthModal').addClass('show').css('display', 'flex');
        $('body').addClass('modal-open');
    }
});

$(document).on('click', '#checkoutAuthModal [data-dismiss="modal"]', function () {
    $('#checkoutAuthModal').removeClass('show').css('display', 'none');
    $('body').removeClass('modal-open');
});
