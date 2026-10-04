/* Requests share the session's form token; jQuery encodes values safely. */
function appPost(url, data, success) {
    jQuery.ajax({
        url: url, type: 'POST', data: data,
        headers: { 'X-CSRF-Token': jQuery('meta[name="csrf-token"]').attr('content') },
        success: success,
        error: function (xhr) { alert(xhr.status < 500 ? xhr.responseText : 'The request could not be completed. Please try again.'); }
    });
}
function send_message() {
    appPost('send_message.php', { name: jQuery('#name').val(), email: jQuery('#email').val(), mobile: jQuery('#mobile').val(), message: jQuery('#message').val() }, function (result) { alert(result); });
}
function user_register() {
    jQuery('.field_error').text('');
    appPost('register_submit.php', { name: jQuery('#name').val(), email: jQuery('#email').val(), mobile: jQuery('#mobile').val(), password: jQuery('#password').val() }, function (result) {
        if (result.trim() === 'email_present') jQuery('#email_error').text('Email already registered.');
        else if (result.trim() === 'insert') { jQuery('.register_msg p').text('Registration complete. You can now sign in.'); jQuery('#register-form')[0].reset(); }
    });
}
function user_login() {
    appPost('login_submit.php', { email: jQuery('#login_email').val(), password: jQuery('#login_password').val() }, function (result) {
        if (result.trim() === 'valid') window.location.href = 'my_order.php';
        else jQuery('.login_msg p').text('Please enter valid login details.');
    });
}
function manage_cart(pid, type) {
    var quantity = type === 'update' ? jQuery('#' + pid + 'qty').val() : (jQuery('#qty').val() || '1');
    appPost('manage_cart.php', { pid: pid, qty: quantity, type: type }, function (result) {
        if (type === 'update' || type === 'remove') window.location.reload();
        else jQuery('.htc__qua').text(result);
    });
}
function sort_product_drop(cat_id, site_path) {
    window.location.href = site_path + 'categories.php?id=' + encodeURIComponent(cat_id) + '&sort=' + encodeURIComponent(jQuery('#sort_product_id').val());
}
function wishlist_manage(pid, type) {
    appPost('wishlist_manage.php', { pid: pid, type: type }, function (result) {
        if (result.trim() === 'not_login') window.location.href = 'login.php';
        else jQuery('.htc__wishlist').text(result);
    });
}
