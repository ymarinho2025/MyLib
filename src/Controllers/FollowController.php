<?php
namespace App\Controllers;
use App\Core\{Database,Response}; use App\Middlewares\AuthMiddleware;
final class FollowController {
  public static function follow(array $p): void { $id=AuthMiddleware::userId(); $target=(int)$p['id']; if($id===$target) Response::json(['error'=>'Não é possível seguir a si mesmo'],422); $st=Database::connection()->prepare('INSERT IGNORE INTO follows(follower_id,followed_id,created_at) VALUES(?,?,NOW())');$st->execute([$id,$target]); Response::json(['message'=>'Seguindo usuário']); }
  public static function unfollow(array $p): void { $id=AuthMiddleware::userId(); $st=Database::connection()->prepare('DELETE FROM follows WHERE follower_id=? AND followed_id=?');$st->execute([$id,(int)$p['id']]); Response::json(['message'=>'Deixou de seguir']); }
}
