<?php
require_once __DIR__ . '/helpers.php';
load_env();
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $url = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? null);
    if (!$url) json_response(['error' => 'DATABASE_URL não configurada'], 500);
    $parts = parse_url($url);
    $host = $parts['host'] ?? '';
    $port = $parts['port'] ?? 5432;
    $db   = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
    $user = urldecode($parts['user'] ?? '');
    $pass = urldecode($parts['pass'] ?? '');
    $query = [];
    parse_str($parts['query'] ?? '', $query);
    $sslmode = $query['sslmode'] ?? 'require';
    $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode={$sslmode}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}
