<?php

declare(strict_types=1);

function to_bool_value(mixed $value): bool
{
    return (int) $value === 1;
}

function build_user_payload(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'username' => (string) $row['username'],
        'nickname' => (string) $row['nickname'],
        'bio' => (string) ($row['bio'] ?? ''),
        'created_at' => (string) $row['created_at'],
    ];
}

function build_post_payload(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'title' => (string) $row['title'],
        'content' => (string) $row['content'],
        'created_at' => (string) $row['created_at'],
        'like_count' => (int) ($row['like_count'] ?? 0),
        'comment_count' => (int) ($row['comment_count'] ?? 0),
        'liked' => to_bool_value($row['liked'] ?? 0),
        'can_delete' => to_bool_value($row['can_delete'] ?? 0),
    ];
}

function build_comment_payload(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'post_id' => (int) $row['post_id'],
        'content' => (string) $row['content'],
        'created_at' => (string) $row['created_at'],
        'like_count' => (int) ($row['like_count'] ?? 0),
        'liked' => to_bool_value($row['liked'] ?? 0),
        'can_delete' => to_bool_value($row['can_delete'] ?? 0),
    ];
}
