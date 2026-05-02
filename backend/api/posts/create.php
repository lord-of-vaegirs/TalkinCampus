<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$userId = require_login();
$title = get_trimmed_post_string('title', '标题', 100);
$content = get_trimmed_post_string('content', '内容', 5000);

$stmt = $pdo->prepare(
    'INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)'
);
$stmt->execute([
    'user_id' => $userId,
    'title' => $title,
    'content' => $content,
]);

respond_success([
    'id' => (int) $pdo->lastInsertId(),
], '发布成功', 201);
