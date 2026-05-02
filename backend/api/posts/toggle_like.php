<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$userId = require_login();
$postId = get_post_int('id', '帖子 id');

$postStmt = $pdo->prepare('SELECT id FROM posts WHERE id = :id LIMIT 1');
$postStmt->execute(['id' => $postId]);
if (!$postStmt->fetch()) {
    respond_not_found('帖子不存在');
}

$checkStmt = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id LIMIT 1');
$checkStmt->execute([
    'post_id' => $postId,
    'user_id' => $userId,
]);
$existing = $checkStmt->fetch();

if ($existing) {
    $stmt = $pdo->prepare('DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id');
    $stmt->execute([
        'post_id' => $postId,
        'user_id' => $userId,
    ]);
    $liked = false;
} else {
    $stmt = $pdo->prepare('INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)');
    $stmt->execute([
        'post_id' => $postId,
        'user_id' => $userId,
    ]);
    $liked = true;
}

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM post_likes WHERE post_id = :post_id');
$countStmt->execute(['post_id' => $postId]);
$likeCount = (int) $countStmt->fetchColumn();

respond_success([
    'liked' => $liked,
    'like_count' => $likeCount,
]);
