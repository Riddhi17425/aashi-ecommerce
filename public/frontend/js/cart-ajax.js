// ==========================================
// CART PAGE AJAX LOGIC (HNOWW Pattern)
// ==========================================

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
                recalculateCartTotals();
                updateHeaderCartCount();
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

                        if ($('#cart_item_list .cart-item-row').length === 0) {
                            let emptyRow = `<tr><td class="text-center" colspan="7">
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

    $('#btn-email-next').click(function() {
        var email = $('#checkout_email').val().trim();
        if (!email) {
            showError('Please enter your email address.');
            return;
        }

        hideError();
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
                    showError(response.message);
                }
            },
            error: function(xhr) {
                $('#btn-email-next').prop('disabled', false).text('Continue');
                showError(getAjaxErrorMessage(xhr));
            }
        });
    });

    $('#btn-login-back, #btn-register-back').click(function() {
        hideError();
        $('#checkoutAuthTitle').text('Login to Checkout');
        $('.auth-step').addClass('d-none');
        $('#step-email').removeClass('d-none');
        $('#checkout_password').val('');
        $('#checkout_name').val('');
        $('#checkout_register_email').val('');
        $('#checkout_reg_password').val('');
        $('#checkout_reg_password_confirmation').val('');
    });

    $('#checkout-auth-form').submit(function(e) {
        e.preventDefault();
        hideError();

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
                    showError(response.message);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text(originalBtnText);
                showError(getAjaxErrorMessage(xhr));
            }
        });
    });

    $('#checkoutAuthModal').on('hidden.bs.modal', function () {
        hideError();
        $('#checkoutAuthTitle').text('Login to Checkout');
        $('.auth-step').addClass('d-none');
        $('#step-email').removeClass('d-none');
        $('#checkout_email').val('');
        $('#checkout_password').val('');
        $('#checkout_name').val('');
        $('#checkout_register_email').val('');
        $('#checkout_reg_password').val('');
        $('#checkout_reg_password_confirmation').val('');
        isRegistered = false;
        userEmail = '';
    });

    function showError(msg) {
        $('#checkout-auth-alert').text(msg).removeClass('d-none');
    }

    function hideError() {
        $('#checkout-auth-alert').addClass('d-none').text('');
    }

    function getAjaxErrorMessage(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }
        return 'Something went wrong. Please try again.';
    }
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