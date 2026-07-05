<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/login/jwt.php';
class BookController {
    public static function all(): void {
        $q = '%' . trim($_GET['q'] ?? '') . '%';
        $stmt = db()->prepare('SELECT b.*, a.name author, g.name genre FROM books b JOIN authors a ON a.id=b.author_id JOIN genres g ON g.id=b.genre_id WHERE b.title ILIKE ? OR a.name ILIKE ? OR g.name ILIKE ? ORDER BY g.name, a.name, b.title');
        $stmt->execute([$q,$q,$q]);
        json_response(['books'=>$stmt->fetchAll()]);
    }
    public static function addToLibrary(): void {
        require_method('POST');
        $userId = auth_user_id(); $d = input_json(); $bookId = (int)($d['book_id'] ?? 0);
        $status = $d['status'] ?? 'WANT_FUTURE';
        $valid = ['READ','READING','NEXT_READ','WANT_FUTURE','DUSTY','GIFT_ACCEPTED','ABANDONED','REREADING','WANT_SPECIAL_EDITION'];
        if (!$bookId || !in_array($status, $valid, true)) json_response(['error'=>'Livro/status inválido.'],422);
        $order = db()->prepare('SELECT COALESCE(MAX(display_order),0)+1 AS n FROM user_books WHERE user_id=?'); $order->execute([$userId]);
        $n = (int)$order->fetch()['n'];
        $stmt = db()->prepare('INSERT INTO user_books (user_id, book_id, status, display_order) VALUES (?,?,?,?) ON CONFLICT (user_id, book_id) DO UPDATE SET status=EXCLUDED.status, updated_at=now() RETURNING *');
        $stmt->execute([$userId,$bookId,$status,$n]);
        json_response(['userBook'=>$stmt->fetch()],201);
    }
    public static function updateUserBook(): void {
        require_method('POST');
        $userId = auth_user_id(); $d = input_json(); $bookId=(int)($d['book_id']??0);
        $fields=[]; $params=[];
        foreach(['status','rating','notes','favorite','display_order'] as $f){ if(array_key_exists($f,$d)){ $fields[]="$f=?"; $params[]=$d[$f]; }}
        if(!$bookId || !$fields) json_response(['error'=>'Nada para atualizar.'],422);
        $params[]=$userId; $params[]=$bookId;
        $sql='UPDATE user_books SET '.implode(',',$fields).', updated_at=now() WHERE user_id=? AND book_id=? RETURNING *';
        $stmt=db()->prepare($sql); $stmt->execute($params); json_response(['userBook'=>$stmt->fetch()]);
    }
    public static function comments(): void {
        require_method('POST');
        $userId=auth_user_id(); $d=input_json(); $bookId=(int)($d['book_id']??0); $body=trim($d['body']??'');
        if(!$bookId||!$body) json_response(['error'=>'Comentário inválido.'],422);
        $stmt=db()->prepare('INSERT INTO book_comments(user_id,book_id,body) VALUES(?,?,?) RETURNING *'); $stmt->execute([$userId,$bookId,$body]); json_response(['comment'=>$stmt->fetch()],201);
    }
}
