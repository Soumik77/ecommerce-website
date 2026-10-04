<?php
// Router for the documented local PHP development server.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if (str_contains($path, "\0") || str_contains($path, '\\') || preg_match('~(?:^|/)\.[^/]*(?:/|$)|^/(?:includes|database|scripts|tests)(?:/|$)|^/config(?:\.|/)|\.inc\.php$|\.(?:sql|md|log|yml|yaml|json)$|^/admin/upload/.*\.(?:php|phtml|phar)$~i', $path)) {
    http_response_code(403); exit('Access denied.');
}
$file = realpath(__DIR__ . $path);
if ($file !== false && !str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR) && $file !== __DIR__) {
    http_response_code(403); exit('Access denied.');
}
if ($path === '/') { require __DIR__ . '/index.php'; return true; }
if (is_file(__DIR__ . $path)) return false;
http_response_code(404); echo 'Page not found.';
