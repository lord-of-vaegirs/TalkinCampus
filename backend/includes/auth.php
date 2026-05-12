<?php

declare(strict_types=1);

function start_session_if_needed(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_path', get_session_cookie_path());

    session_start();
}

function get_session_cookie_path(): string
{
    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $backendPos = strpos($scriptName, '/backend/');

    if ($backendPos === false) {
        return '/';
    }

    $basePath = substr($scriptName, 0, $backendPos);
    return $basePath === '' ? '/' : $basePath . '/';
}

function get_current_user_id(): ?int
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $userId = (int) $_SESSION['user_id'];
    return $userId > 0 ? $userId : null;
}

function require_login(): int
{
    $userId = get_current_user_id();
    if ($userId === null) {
        respond_unauthorized('请先登录后再操作');
    }

    return $userId;
}

function login_user(int $userId): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function get_current_user_or_fail(PDO $pdo): array
{
    $userId = require_login();

    $stmt = $pdo->prepare('SELECT id, username, nickname, bio, created_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        logout_user();
        respond_unauthorized('登录状态已失效，请重新登录');
    }

    return $user;
}
