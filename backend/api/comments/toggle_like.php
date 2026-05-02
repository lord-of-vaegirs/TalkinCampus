<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$userId = require_login();
$commentId = get_post_int('id', '评论 id');

$commentStmt = $pdo->prepare('SELECT id FROM comments WHERE id = :id LIMIT 1');
$commentStmt->execute(['id' => $commentId]);
if (!$commentStmt->fetch()) {
    respond_not_found('评论不存在');
}

$checkStmt = $pdo->prepare(
    'SELECT id FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id LIMIT 1'
);
$checkStmt->execute([
    'comment_id' => $commentId,
    'user_id' => $userId,
]);
$existing = $checkStmt->fetch();

if ($existing) {
    $stmt = $pdo->prepare('DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
    $stmt->execute([
        'comment_id' => $commentId,
        'user_id' => $userId,
    ]);
    $liked = false;
} else {
    $stmt = $pdo->prepare('INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)');
    $stmt->execute([
        'comment_id' => $commentId,
        'user_id' => $userId,
    ]);
    $liked = true;
}

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM comment_likes WHERE comment_id = :comment_id');
$countStmt->execute(['comment_id' => $commentId]);
$likeCount = (int) $countStmt->fetchColumn();

respond_success([
    'liked' => $liked,
    'like_count' => $likeCount,
]);
