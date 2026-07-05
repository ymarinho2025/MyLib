<?php
namespace App\Controllers;
use App\Core\{Database,Response}; use App\Middlewares\AuthMiddleware;
final class FeedController { public static function index(): void { $id=AuthMiddleware::userId(); $sql='SELECT ub.updated_at,u.name,CONCAT("@",u.username) username,b.title,a.name author,g.name genre,ub.status,ub.rating,ub.notes,ub.favorite FROM follows f JOIN user_books ub ON ub.user_id=f.followed_id JOIN users u ON u.id=ub.user_id JOIN books b ON b.id=ub.book_id JOIN authors a ON a.id=b.author_id JOIN genres g ON g.id=b.genre_id WHERE f.follower_id=? ORDER BY ub.updated_at DESC LIMIT 100'; $st=Database::connection()->prepare($sql);$st->execute([$id]); Response::json($st->fetchAll()); }}
