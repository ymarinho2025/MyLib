<?php
require_once dirname(__DIR__) . '/helpers.php';
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
function jwt_secret(): string {
    load_env();
    return getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? 'dev_secret_change_me');
}
function make_token(array $user): string {
    return JWT::encode([
        'sub' => $user['id'],
        'username' => $user['username'],
        'iat' => time(),
        'exp' => time() + 60 * 60 * 24 * 7
    ], jwt_secret(), 'HS256');
}
function auth_user_id(): int {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/Bearer\s+(.*)$/i', $header, $m)) json_response(['error' => 'Token ausente'], 401);
    try {
        $payload = JWT::decode($m[1], new Key(jwt_secret(), 'HS256'));
        return (int) $payload->sub;
    } catch (Throwable $e) {
        json_response(['error' => 'Token inválido'], 401);
    }
}
