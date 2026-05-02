<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('GET');

$user = get_current_user_or_fail($pdo);
$userId = (int) $user['id'];

$postCountStmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE user_id = :user_id');
$postCountStmt->execute(['user_id' => $userId]);
$postCount = (int) $postCountStmt->fetchColumn();

$commentCountStmt = $pdo->prepare('SELECT COUNT(*) FROM comments WHERE user_id = :user_id');
$commentCountStmt->execute(['user_id' => $userId]);
$commentCount = (int) $commentCountStmt->fetchColumn();

$likesStmt = $pdo->prepare(
    'SELECT
        (
            SELECT COUNT(*)
            FROM post_likes pl
            INNER JOIN posts p ON p.id = pl.post_id
            WHERE p.user_id = :post_user_id
        ) +
        (
            SELECT COUNT(*)
            FROM comment_likes cl
            INNER JOIN comments c ON c.id = cl.comment_id
            WHERE c.user_id = :comment_user_id
        ) AS total_likes'
);
$likesStmt->execute([
    'post_user_id' => $userId,
    'comment_user_id' => $userId,
]);
$totalLikes = (int) $likesStmt->fetchColumn();

respond_success([
    'post_count' => $postCount,
    'comment_count' => $commentCount,
    'total_likes' => $totalLikes,
]);
