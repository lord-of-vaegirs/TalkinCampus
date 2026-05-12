<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$username = get_trimmed_post_string('username', '用户名', 50);
$nickname = get_trimmed_post_string('nickname', '昵称', 50);
$password = (string) ($_POST['password'] ?? '');

if ($password === '') {
    respond_error('密码不能为空');
}

if (string_length($password) < 6) {
    respond_error('密码长度不能少于 6 个字符');
}

if (string_length($password) > 255) {
    respond_error('密码长度不能超过 255 个字符');
}

$checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
$checkStmt->execute(['username' => $username]);
if ($checkStmt->fetch()) {
    respond_error('用户名已存在', 409);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = $pdo->prepare(
    'INSERT INTO users (username, password_hash, nickname, bio) VALUES (:username, :password_hash, :nickname, :bio)'
);
$insertStmt->execute([
    'username' => $username,
    'password_hash' => $passwordHash,
    'nickname' => $nickname,
    'bio' => '',
]);

$userId = (int) $pdo->lastInsertId();
$userStmt = $pdo->prepare('SELECT id, username, nickname, bio, created_at FROM users WHERE id = :id LIMIT 1');
$userStmt->execute(['id' => $userId]);
$user = $userStmt->fetch();

respond_success([
    'user' => build_user_payload($user),
], '注册成功', 201);
