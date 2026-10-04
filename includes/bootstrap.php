<?php
declare(strict_types=1);

function app_config(string $key, mixed $default = ''): mixed
{
    static $local = null;
    if ($local === null) {
        $path = dirname(__DIR__) . '/config.local.php';
        $local = is_file($path) ? require $path : [];
    }
    $value = getenv($key);
    return $value !== false ? $value : ($local[$key] ?? $default);
}

if (PHP_SAPI !== 'cli' && session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'use_strict_mode' => true, 'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure' => str_starts_with((string) app_config('APP_URL'), 'https://'),
    ]);
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
}
if (!defined('SERVER_PATH')) {
    define('SERVER_PATH', dirname(__DIR__) . '/');
    define('SITE_PATH', rtrim((string) app_config('APP_URL', 'http://127.0.0.1:8000/'), '/') . '/');
    define('PRODUCT_IMAGE_SERVER_PATH', SERVER_PATH . 'admin/upload/');
    define('PRODUCT_IMAGE_SITE_PATH', SITE_PATH . 'admin/upload/');
}

function db(): mysqli
{
    static $connection = null;
    if (!$connection) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $connection = new mysqli(
            (string) app_config('DB_HOST', '127.0.0.1'),
            (string) app_config('DB_USER', 'ecommerce_user'),
            (string) app_config('DB_PASSWORD'),
            (string) app_config('DB_NAME', 'ecommerce_portfolio'),
            (int) app_config('DB_PORT', 3306), app_config('DB_SOCKET') ?: null
        );
        $connection->set_charset('utf8mb4');
    }
    return $connection;
}

function db_query(string $sql, array $parameters = []): mysqli_result|int
{
    $statement = db()->prepare($sql);
    if ($parameters) {
        $types = implode('', array_map(fn ($value) => is_int($value) ? 'i' : (is_float($value) ? 'd' : 's'), $parameters));
        $statement->bind_param($types, ...$parameters);
    }
    $statement->execute();
    $affected = $statement->affected_rows;
    $result = $statement->get_result();
    $statement->close();
    return $result === false ? $affected : $result;
}

function db_one(string $sql, array $parameters = []): ?array
{
    return db_query($sql, $parameters)->fetch_assoc();
}
function h(mixed $value): string
{
    return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function input_string(array $source, string $key, int $limit = 255): string
{
    $value = $source[$key] ?? '';
    if (!is_string($value) || strlen($value) > $limit) throw new InvalidArgumentException('A field is invalid or too long.');
    return trim($value);
}
function positive_int(mixed $value, string $field = 'ID', int $maximum = 2147483647): int
{
    $number = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => $maximum]]);
    if ($number === false) throw new InvalidArgumentException($field . ' must be a positive whole number.');
    return $number;
}
function money_cents(mixed $value): int
{
    if (!is_scalar($value) || !preg_match('/^(0|[1-9][0-9]{0,6})(?:\.([0-9]{1,2}))?$/D', (string) $value, $parts)) throw new InvalidArgumentException('Price must be non-negative with at most two decimal places.');
    return ((int) $parts[1] * 100) + (int) str_pad($parts[2] ?? '', 2, '0');
}
function money_string(int $cents): string
{
    return intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
}
function csrf_token(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}
function require_csrf(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405); header('Allow: POST'); exit('Use a POST request.');
    }
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403); exit('The form expired. Refresh the page and try again.');
    }
}
function redirect_to(string $path): never
{
    header('Location: ' . SITE_PATH . ltrim($path, '/'), true, 303); exit;
}
function current_user(): ?array
{
    $id = $_SESSION['USER_ID'] ?? 0;
    return $id ? db_one('SELECT id, name, email FROM users WHERE id = ? AND status = 1', [(int) $id]) : null;
}
function require_user(): array
{
    $user = current_user();
    if (!$user) {
        unset($_SESSION['USER_LOGIN'], $_SESSION['USER_ID'], $_SESSION['USER_NAME']);
        redirect_to('login.php');
    }
    return $user;
}
function require_admin(): void
{
    if (empty($_SESSION['ADMIN_ID']) || ($_SESSION['ADMIN_LOGIN'] ?? '') !== 'yes') redirect_to('admin/login.php');
}
function post_button(string $action, int $id, string $label): string
{
    return '<form method="post" style="display:inline-block;margin:2px">' . csrf_field()
        . '<input type="hidden" name="id" value="' . $id . '">'
        . '<button class="btn btn-sm btn-outline-secondary" name="action" value="' . h($action) . '">' . h($label) . '</button></form>';
}
if (PHP_SAPI !== 'cli') {
    set_exception_handler(function (Throwable $error): void {
        error_log('Application error: ' . get_class($error) . ' in ' . basename($error->getFile()) . ':' . $error->getLine());
        http_response_code($error instanceof InvalidArgumentException ? 422 : 500);
        echo $error instanceof InvalidArgumentException ? h($error->getMessage()) : 'The request could not be completed. Check the local setup and server log.';
    });
}
