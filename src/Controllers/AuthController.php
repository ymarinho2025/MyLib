<?php
namespace App\Controllers;
use App\Core\{Database,Request,Response}; use App\Services\JwtService; use App\Middlewares\AuthMiddleware;
final class AuthController {
  private static function cleanUsername(string $u): string { $u=strtolower(trim($u)); $u=ltrim($u,'@'); if(!preg_match('/^[a-z0-9._]+$/',$u)) Response::json(['error'=>'Username deve ter apenas letras minúsculas, números, ponto e underline'],422); return $u; }
  private static function publicUser(array $u): array { unset($u['password_hash']); $u['username']='@'.$u['username']; return $u; }
  public static function register(): void { $b=Request::body(); foreach(['name','username','email','password'] as $f) if(empty($b[$f])) Response::json(['error'=>"Campo obrigatório: $f"],422); $pdo=Database::connection(); $username=self::cleanUsername($b['username']); $email=strtolower(trim($b['email']));
    $exists=$pdo->prepare('SELECT id FROM users WHERE username=? OR email=?'); $exists->execute([$username,$email]); if($exists->fetch()) Response::json(['error'=>'Username ou e-mail já cadastrado'],409);
    $hash=password_hash($b['password'], PASSWORD_BCRYPT); $st=$pdo->prepare('INSERT INTO users(name,username,email,password_hash,bio,avatar,created_at,updated_at) VALUES(?,?,?,?,?,?,NOW(),NOW())'); $st->execute([trim($b['name']),$username,$email,$hash,$b['bio']??null,$b['avatar']??null]); $id=(int)$pdo->lastInsertId();
    $token=JwtService::sign(['id'=>$id,'username'=>$username]); Response::json(['token'=>$token,'user'=>['id'=>$id,'name'=>$b['name'],'username'=>'@'.$username,'email'=>$email,'bio'=>$b['bio']??null]],201); }
  public static function login(): void { $b=Request::body(); if(empty($b['login'])||empty($b['password'])) Response::json(['error'=>'Informe login e senha'],422); $login=strtolower(ltrim(trim($b['login']),'@')); $pdo=Database::connection(); $st=$pdo->prepare('SELECT * FROM users WHERE email=? OR username=? LIMIT 1'); $st->execute([$login,$login]); $u=$st->fetch(); if(!$u||!password_verify($b['password'],$u['password_hash'])) Response::json(['error'=>'Credenciais inválidas'],401); $token=JwtService::sign(['id'=>(int)$u['id'],'username'=>$u['username']]); Response::json(['token'=>$token,'user'=>self::publicUser($u)]); }
  public static function me(): void { $id=AuthMiddleware::userId(); $pdo=Database::connection(); $st=$pdo->prepare('SELECT id,name,username,email,bio,avatar,created_at,updated_at FROM users WHERE id=?'); $st->execute([$id]); $u=$st->fetch(); if(!$u) Response::json(['error'=>'Usuário não encontrado'],404); $u['username']='@'.$u['username']; Response::json($u); }
}
