<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('POST');
require_login();

logout_user();

respond_success([], '退出成功');
