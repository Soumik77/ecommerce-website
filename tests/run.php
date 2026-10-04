<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
$passed = 0;
function check(bool $condition, string $label): void
{
    global $passed;
    if (!$condition) throw new RuntimeException('FAIL: ' . $label);
    $passed++; echo "PASS: $label\n";
}
function rejects(callable $operation, string $label): void
{
    try { $operation(); } catch (InvalidArgumentException $error) { check(true, $label); return; }
    check(false, $label);
}
check(money_cents('19.99') * 2 === 3998, 'Two 19.99 items total exactly 39.98');
check(money_cents('0.1') * 3 === 30, 'Decimal arithmetic uses integer cents');
check(money_string(3998) === '39.98', 'Order totals serialize with two decimal places');
check(money_string(0) === '0.00', 'Zero amount serializes correctly');
foreach (['-1','1.234','1e3','NaN',[], '10000000'] as $value) rejects(fn () => money_cents($value), 'Reject malformed price ' . json_encode($value));
foreach ([0,-2,'1.5','1 OR 1=1',null,[]] as $value) rejects(fn () => positive_int($value), 'Reject invalid ID or quantity ' . json_encode($value));
check(positive_int('4', 'Quantity', 999) === 4, 'Accept a valid quantity');
rejects(fn () => positive_int(1000, 'Quantity', 999), 'Enforce maximum quantity');
rejects(fn () => input_string(['name' => ['unexpected']], 'name'), 'Reject array input for a text field');
rejects(fn () => input_string(['name' => 'long'], 'name', 3), 'Enforce text length');
check(h('<script>alert("x")</script>') === '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', 'Escape text inserted into HTML');
check(h(['unexpected']) === '', 'Non-scalar display values do not become HTML');
$_SESSION = [];
$token = csrf_token();
check(strlen($token) === 64 && csrf_token() === $token, 'Form token persists within the session');
echo "$passed checks passed. These checks do not require MySQL.\n";
