<?php
require_once dirname(__DIR__) . '/db.php';
require_once __DIR__ . '/jwt.php';
class AuthController
{
    public static function register(): void
    {
        require_method('POST');
        $d = input_json();
        $name = trim($d['name'] ?? '');
        $username = clean_username($d['username'] ?? '');
        $email = strtolower(trim($d['email'] ?? ''));
        $password = $d['password'] ?? '';
        $bio = trim($d['bio'] ?? '');
        if (!$name || !$username || !$email || strlen($password) < 6) {
            json_response([
                'error' => 'Dados inválidos.',
                'debug' => [
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'password_length' => strlen($password),
                    'received_keys' => array_keys($d)
                ]
            ], 422);
        }
        if (!preg_match('/^[a-z0-9._]+$/', $username))
            json_response(['error' => 'Username aceita apenas letras minúsculas, números, ponto e underline.'], 422);
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = db()->prepare('INSERT INTO users (name, username, email, password_hash, bio) VALUES (?, ?, ?, ?, ?) RETURNING id, name, username, email, bio, avatar');
            $stmt->execute([$name, $username, $email, $hash, $bio]);
            $user = $stmt->fetch();
            json_response(['user' => $user, 'token' => make_token($user)], 201);
        } catch (PDOException $e) {
            if ($e->getCode() === '23505')
                json_response(['error' => 'E-mail ou username já cadastrado.'], 409);
            throw $e;
        }
    }
    public static function login(): void
    {
        require_method('POST');
        $d = input_json();
        $login = strtolower(ltrim(trim($d['login'] ?? ''), '@'));
        $password = $d['password'] ?? '';
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash']))
            json_response(['error' => 'Login ou senha inválidos.'], 401);
        unset($user['password_hash']);
        json_response(['user' => $user, 'token' => make_token($user)]);
    }
    public static function me(): void
    {
        $id = auth_user_id();
        $stmt = db()->prepare('SELECT id, name, username, email, bio, avatar, pix_key_type, pix_key, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        json_response(['user' => $stmt->fetch()]);
    }
}
