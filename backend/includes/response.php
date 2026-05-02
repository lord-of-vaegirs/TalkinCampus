<?php

declare(strict_types=1);

function set_json_headers(): void
{
    header('Content-Type: application/json; charset=utf-8');
}

function normalize_response_data(array $data): array|object
{
    return $data === [] ? (object) [] : $data;
}

function send_json(bool $success, string $message = 'ok', array $data = [], int $statusCode = 200): void
{
    http_response_code($statusCode);
    set_json_headers();

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => normalize_response_data($data),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;
}

function respond_success(array $data = [], string $message = 'ok', int $statusCode = 200): void
{
    send_json(true, $message, $data, $statusCode);
}

function respond_error(string $message, int $statusCode = 400, array $data = []): void
{
    send_json(false, $message, $data, $statusCode);
}

function respond_not_found(string $message = '资源不存在'): void
{
    respond_error($message, 404);
}

function respond_unauthorized(string $message = '请先登录'): void
{
    respond_error($message, 401);
}

function respond_forbidden(string $message = '无权执行此操作'): void
{
    respond_error($message, 403);
}

function respond_method_not_allowed(string $message = '请求方法不支持'): void
{
    respond_error($message, 405);
}

function respond_server_error(string $message = '服务器繁忙，请稍后再试'): void
{
    respond_error($message, 500);
}
