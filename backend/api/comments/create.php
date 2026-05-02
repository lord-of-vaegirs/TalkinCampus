<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$userId = require_login();
$postId = get_post_int('post_id', '帖子 id');
$content = get_trimmed_post_string('content', '评论内容', 2000);

$postStmt = $pdo->prepare('SELECT id FROM posts WHERE id = :id LIMIT 1');
$postStmt->execute(['id' => $postId]);
if (!$postStmt->fetch()) {
    respond_not_found('帖子不存在');
}

$stmt = $pdo->prepare(
    'INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)'
);
$stmt->execute([
    'post_id' => $postId,
    'user_id' => $userId,
    'content' => $content,
]);

respond_success([
    'id' => (int) $pdo->lastInsertId(),
], '评论发布成功', 201);
