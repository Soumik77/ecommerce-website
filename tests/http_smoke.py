"""HTTP regression checks for a disposable local demo database only."""
import html
import http.cookiejar
import os
import re
import time
import urllib.error
import urllib.parse
import urllib.request

BASE = os.environ.get('APP_URL', 'http://127.0.0.1:8000/').rstrip('/') + '/'
if os.environ.get('ECOM_ALLOW_INTEGRATION_TESTS') != '1' or not os.environ.get('DB_NAME', '').endswith('_test'):
    raise SystemExit('Refused: enable integration tests against a disposable *_test database.')
if urllib.parse.urlparse(BASE).hostname not in ('127.0.0.1', 'localhost'):
    raise SystemExit('HTTP checks only run against localhost.')

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, request, file, code, message, headers, new_url):
        return None

class Client:
    def __init__(self):
        self.opener = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), NoRedirect()
        )

    def request(self, path='', data=None, token=None):
        headers = {'X-CSRF-Token': token} if token else {}
        body = urllib.parse.urlencode(data).encode() if data is not None else None
        request = urllib.request.Request(BASE + path.lstrip('/'), data=body, headers=headers)
        try:
            response = self.opener.open(request, timeout=10)
        except urllib.error.HTTPError as error:
            response = error
        return response.code, response.headers, response.read().decode('utf-8', errors='replace')

passed = 0
def check(condition, label):
    global passed
    if not condition:
        raise AssertionError(label)
    passed += 1
    print('PASS:', label, flush=True)

def token_from(body):
    match = re.search(r'name="(?:csrf-token|csrf_token)" (?:content|value)="([^"]+)"', body)
    if not match:
        raise AssertionError('Rendered page has no form token')
    return html.unescape(match.group(1))

def login(client, email):
    status, _, body = client.request('login.php')
    check(status == 200, 'Login form loads')
    token = token_from(body)
    status, _, body = client.request('login_submit.php', {'email': email, 'password': 'PortfolioDemo!2026'}, token)
    check(status == 200 and body.strip() == 'valid', 'Synthetic customer can sign in')
    return token

anonymous = Client()
for attempt in range(30):
    try:
        if anonymous.request('login.php')[0] == 200:
            break
    except urllib.error.URLError:
        pass
    time.sleep(0.2)
else:
    raise AssertionError('Local PHP server did not become ready')

for path in ['', 'categories.php?id=1&sort=price_low', 'product.php?id=1',
             'search.php?str=shirt', 'login.php', 'cart.php', 'contact.php',
             'forgot_password.php', 'admin/upload/demo-shirt.svg']:
    status, _, body = anonymous.request(path)
    check(status == 200 and not re.search(r'(?:Warning|Fatal error|Parse error):', body), 'Page loads without PHP errors: ' + (path or '/'))

for path in ['database/ecom.sql', 'config.example.php', 'includes/bootstrap.php', '.git/config']:
    check(anonymous.request(path)[0] == 403, 'Private path blocked: ' + path)
check(anonymous.request('my_order.php')[0] == 303, 'Order history requires login')
check(anonymous.request('admin/product.php')[0] == 303, 'Admin pages require admin login')
check(anonymous.request('login_submit.php', {'email': 'customer.one@example.test', 'password': 'PortfolioDemo!2026'})[0] == 403, 'Login rejects a missing form token')
for path in ['forgot_password_submit.php', 'send_otp.php', 'check_otp.php']:
    check(anonymous.request(path, {})[0] == 410, 'Incomplete outbound endpoint disabled: ' + path)

registration_token = token_from(anonymous.request('login.php')[2])
registration = {'name': '<b>Demo Registration</b>', 'email': 'http-test@example.test',
                'mobile': '+10000000003', 'password': 'Strong&+Demo=2026'}
status, _, body = anonymous.request('register_submit.php', registration, registration_token)
check(status == 200 and body.strip() == 'insert', 'Registration accepts encoded punctuation in the password')
check(anonymous.request('register_submit.php', registration, registration_token)[2].strip() == 'email_present', 'Duplicate email is rejected')
status, _, body = anonymous.request('login_submit.php', {'email': registration['email'], 'password': registration['password']}, registration_token)
check(status == 200 and body.strip() == 'valid', 'New hashed password can authenticate')

alice, bob = Client(), Client()
alice_token = login(alice, 'customer.one@example.test')
bob_token = login(bob, 'customer.two@example.test')
check(alice.request('my_order_details.php?id=1001')[0] == 200, 'Customer can view their own order')
check(bob.request('my_order_details.php?id=1001')[0] == 404, 'Other customer cannot view that order')
check(alice.request('my_order_details.php?id=1%20OR%201=1')[0] == 404, 'Malformed order ID is rejected')
check(alice.request('manage_cart.php', {'pid': 1, 'qty': -2, 'type': 'add'}, alice_token)[0] == 422, 'Negative cart quantity is rejected')
check(alice.request('manage_cart.php', {'pid': 1, 'qty': 2, 'type': 'add'}, alice_token)[0] == 200, 'Valid product enters the cart')
checkout_token = token_from(alice.request('checkout.php')[2])
status, headers, _ = alice.request('checkout.php', {'address': '4 Example Road', 'city': 'Demo City', 'pincode': '00004', 'payment_type': 'COD', 'csrf_token': checkout_token})
check(status == 303 and headers.get('Location', '').endswith('thank_you.php'), 'Checkout redirects after saving')
status, _, body = alice.request('thank_you.php')
match = re.search(r'my_order_details.php\?id=(\d+)', body)
check(status == 200 and match is not None, 'Confirmation links to the created order')
order_id = match.group(1)
check('39.98' in alice.request('my_order_details.php?id=' + order_id)[2], 'Saved order displays the expected total')
check(bob.request('my_order_details.php?id=' + order_id)[0] == 404, 'New order also enforces ownership')

admin = Client()
admin_token = token_from(admin.request('admin/login.php')[2])
status, _, _ = admin.request('admin/login.php', {'username': 'demo_admin', 'password': 'PortfolioDemo!2026', 'csrf_token': admin_token})
check(status == 303, 'Administrator can sign in with a hashed password')
for path in ['admin/categories.php', 'admin/product.php', 'admin/users.php', 'admin/contact_us.php',
             'admin/order_master.php', 'admin/order_master_detail.php?id=1001',
             'admin/manage_categories.php?id=1', 'admin/manage_product.php?id=1']:
    status, _, body = admin.request(path)
    check(status == 200 and not re.search(r'(?:Warning|Fatal error|Parse error):', body), 'Admin page loads: ' + path)
users_page = admin.request('admin/users.php')[2]
check('&lt;b&gt;Demo Registration&lt;/b&gt;' in users_page and '<b>Demo Registration</b>' not in users_page, 'Stored customer name is escaped in the admin view')
check(admin.request('admin/categories.php', {'action': 'deactivate', 'id': 1})[0] == 403, 'Admin mutation rejects a missing form token')
check(admin.request('admin/categories.php', {'action': 'deactivate', 'id': 1, 'csrf_token': admin_token})[0] == 303, 'Category can be deactivated')
check(anonymous.request('product.php?id=1')[0] == 404, 'Inactive category hides its products')
check(admin.request('admin/categories.php', {'action': 'activate', 'id': 1, 'csrf_token': admin_token})[0] == 303, 'Category can be reactivated')
check(alice.request('logout.php', {})[0] == 403, 'Logout requires a form token')
check(alice.request('logout.php', {'csrf_token': alice_token})[0] == 303, 'Customer can log out')
check(alice.request('my_order.php')[0] == 303, 'Logged-out customer cannot view orders')
print(f'{passed} HTTP checks passed against the disposable local demo.')
