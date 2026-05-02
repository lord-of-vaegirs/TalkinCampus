<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('GET');

$user = get_current_user_or_fail($pdo);

respond_success([
    'user' => build_user_payload($user),
]);
