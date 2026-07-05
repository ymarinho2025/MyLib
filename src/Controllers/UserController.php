<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/login/jwt.php';
class UserController {
    public static function search(): void {
        $q = '%' . strtolower(ltrim(trim($_GET['q'] ?? ''), '@')) . '%';
        $stmt = db()->prepare('SELECT u.id,u.name,u.username,u.bio,u.avatar, COUNT(ub.id) total_books FROM users u LEFT JOIN user_books ub ON ub.user_id=u.id WHERE LOWER(u.name) LIKE ? OR LOWER(u.username) LIKE ? GROUP BY u.id ORDER BY u.name');
        $stmt->execute([$q,$q]); json_response(['users'=>$stmt->fetchAll()]);
    }
    public static function profile(): void {
        $username = clean_username($_GET['username'] ?? '');
        $stmt = db()->prepare('SELECT id,name,username,bio,avatar,created_at FROM users WHERE username=?'); $stmt->execute([$username]);
        $user=$stmt->fetch(); if(!$user) json_response(['error'=>'Usuário não encontrado'],404);
        $books=db()->prepare('SELECT ub.status,ub.rating,ub.notes,ub.favorite,ub.display_order,b.title,a.name author,g.name genre FROM user_books ub JOIN books b ON b.id=ub.book_id JOIN authors a ON a.id=b.author_id JOIN genres g ON g.id=b.genre_id WHERE ub.user_id=? ORDER BY ub.status, ub.display_order, g.name, b.title');
        $books->execute([$user['id']]);
        $stats=db()->prepare('SELECT status, COUNT(*) total FROM user_books WHERE user_id=? GROUP BY status'); $stats->execute([$user['id']]);
        json_response(['user'=>$user,'stats'=>$stats->fetchAll(),'books'=>$books->fetchAll()]);
    }
    public static function update(): void {
        require_method('POST'); $id=auth_user_id(); $d=input_json();
        $stmt=db()->prepare('UPDATE users SET name=COALESCE(?,name), bio=COALESCE(?,bio), avatar=COALESCE(?,avatar), updated_at=now() WHERE id=? RETURNING id,name,username,email,bio,avatar');
        $stmt->execute([$d['name']??null,$d['bio']??null,$d['avatar']??null,$id]); json_response(['user'=>$stmt->fetch()]);
    }
    public static function pix(): void {
        require_method('POST'); $id=auth_user_id(); $d=input_json();
        $stmt=db()->prepare('UPDATE users SET pix_key_type=?, pix_key=?, updated_at=now() WHERE id=? RETURNING id,pix_key_type,pix_key');
        $stmt->execute([$d['pix_key_type']??'aleatoria',$d['pix_key']??'', $id]); json_response(['pix'=>$stmt->fetch()]);
    }
}
