<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$userId = require_login();
$commentId = get_post_int('id', '评论 id');

$stmt = $pdo->prepare('SELECT user_id FROM comments WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $commentId]);
$comment = $stmt->fetch();

if (!$comment) {
    respond_not_found('评论不存在');
}

if ((int) $comment['user_id'] !== $userId) {
    respond_forbidden('只能删除自己发布的评论');
}

$deleteStmt = $pdo->prepare('DELETE FROM comments WHERE id = :id');
$deleteStmt->execute(['id' => $commentId]);

respond_success([], '删除成功');
