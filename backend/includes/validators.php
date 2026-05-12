<?php

declare(strict_types=1);

function require_method(string $expectedMethod): void
{
    $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $expectedMethod = strtoupper($expectedMethod);

    if ($requestMethod !== $expectedMethod) {
        header('Allow: ' . $expectedMethod);
        respond_method_not_allowed('请求方法不支持');
    }
}

function get_trimmed_post_string(string $key, string $label, int $maxLength, int $minLength = 1): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    validate_string_length($value, $label, $maxLength, $minLength);

    return $value;
}

function get_trimmed_query_string(string $key, string $label, int $maxLength, int $minLength = 1): string
{
    $value = trim((string) ($_GET[$key] ?? ''));
    validate_string_length($value, $label, $maxLength, $minLength);

    return $value;
}

function get_post_int(string $key, string $label): int
{
    return parse_positive_int($_POST[$key] ?? null, $label);
}

function get_query_int(string $key, string $label): int
{
    return parse_positive_int($_GET[$key] ?? null, $label);
}

function get_page_value(): int
{
    return get_optional_positive_int($_GET['page'] ?? null, 1, 1, 1000000);
}

function get_page_size_value(): int
{
    return get_optional_positive_int($_GET['page_size'] ?? null, 20, 1, 100);
}

function validate_string_length(string $value, string $label, int $maxLength, int $minLength = 1): void
{
    $length = string_length($value);

    if ($length < $minLength) {
        respond_error($label . '不能为空');
    }

    if ($length > $maxLength) {
        respond_error($label . '长度不能超过' . $maxLength . '个字符');
    }
}

function string_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value);
    }

    return strlen($value);
}

function get_optional_positive_int(mixed $value, int $defaultValue, int $minValue = 1, int $maxValue = PHP_INT_MAX): int
{
    if ($value === null || $value === '') {
        return $defaultValue;
    }

    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
        respond_error('参数格式不正确');
    }

    $intValue = (int) $value;
    if ($intValue < $minValue || $intValue > $maxValue) {
        respond_error('参数超出允许范围');
    }

    return $intValue;
}

function parse_positive_int(mixed $value, string $label): int
{
    if ($value === null || $value === '') {
        respond_error($label . '不能为空');
    }

    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
        respond_error($label . '必须是整数');
    }

    $intValue = (int) $value;
    if ($intValue <= 0) {
        respond_error($label . '必须大于 0');
    }

    return $intValue;
}
