<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');

$username = get_trimmed_post_string('username', '用户名', 50);
$password = (string) ($_POST['password'] ?? '');

if ($password === '') {
    respond_error('密码不能为空');
}

$stmt = $pdo->prepare(
    'SELECT id, username, password_hash, nickname, bio, created_at
     FROM users
     WHERE username = :username
     LIMIT 1'
);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, (string) $user['password_hash'])) {
    respond_error('用户名或密码错误', 401);
}

login_user((int) $user['id']);

respond_success([
    'user' => build_user_payload($user),
], '登录成功');
