<?php
function load_env(): void {
    $file = dirname(__DIR__, 2) . '/.env';
    if (!file_exists($file)) return;
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v, " \t\n\r\0\x0B\"");
    }
}
function json_response($data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
function input_json(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $_POST;
}
function clean_username(string $u): string {
    $u = strtolower(trim($u));
    return ltrim($u, '@');
}
function require_method(string $method): void {
    if ($_SERVER['REQUEST_METHOD'] !== $method) json_response(['error' => 'Método não permitido'], 405);
}
