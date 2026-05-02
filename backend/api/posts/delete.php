<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$userId = require_login();
$postId = get_post_int('id', '帖子 id');

$stmt = $pdo->prepare('SELECT user_id FROM posts WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $postId]);
$post = $stmt->fetch();

if (!$post) {
    respond_not_found('帖子不存在');
}

if ((int) $post['user_id'] !== $userId) {
    respond_forbidden('只能删除自己发布的帖子');
}

$deleteStmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
$deleteStmt->execute(['id' => $postId]);

respond_success([], '删除成功');
