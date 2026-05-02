<?php

declare(strict_types=1);

require_once __DIR__ . '/response.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/validators.php';
require_once __DIR__ . '/serializers.php';
require_once __DIR__ . '/../config/database.php';

set_json_headers();
start_session_if_needed();

set_exception_handler(function (Throwable $exception): void {
    error_log((string) $exception);
    respond_server_error();
});
