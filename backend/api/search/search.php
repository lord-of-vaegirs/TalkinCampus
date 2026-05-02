<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('GET');

$query = get_trimmed_query_string('q', '搜索关键词', 100);
$page = get_page_value();
$pageSize = get_page_size_value();
$offset = ($page - 1) * $pageSize;
$viewerId = get_current_user_id() ?? 0;
$likeQuery = '%' . $query . '%';

$countStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM posts WHERE title LIKE :title_keyword OR content LIKE :content_keyword'
);
$countStmt->execute([
    'title_keyword' => $likeQuery,
    'content_keyword' => $likeQuery,
]);
$total = (int) $countStmt->fetchColumn();

$sql = '
    SELECT
        p.id,
        p.title,
        p.content,
        p.created_at,
        COALESCE(pl.like_count, 0) AS like_count,
        COALESCE(cc.comment_count, 0) AS comment_count,
        CASE WHEN vpl.user_id IS NULL THEN 0 ELSE 1 END AS liked,
        CASE WHEN p.user_id = :viewer_id THEN 1 ELSE 0 END AS can_delete
    FROM posts p
    LEFT JOIN (
        SELECT post_id, COUNT(*) AS like_count
        FROM post_likes
        GROUP BY post_id
    ) pl ON pl.post_id = p.id
    LEFT JOIN (
        SELECT post_id, COUNT(*) AS comment_count
        FROM comments
        GROUP BY post_id
    ) cc ON cc.post_id = p.id
    LEFT JOIN post_likes vpl
        ON vpl.post_id = p.id
       AND vpl.user_id = :viewer_like_user_id
    WHERE p.title LIKE :title_keyword OR p.content LIKE :content_keyword
    ORDER BY p.created_at DESC, p.id DESC
    LIMIT :limit OFFSET :offset
';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':viewer_id', $viewerId, PDO::PARAM_INT);
$stmt->bindValue(':viewer_like_user_id', $viewerId, PDO::PARAM_INT);
$stmt->bindValue(':title_keyword', $likeQuery, PDO::PARAM_STR);
$stmt->bindValue(':content_keyword', $likeQuery, PDO::PARAM_STR);
$stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$items = array_map('build_post_payload', $stmt->fetchAll());

respond_success([
    'items' => $items,
    'page' => $page,
    'page_size' => $pageSize,
    'total' => $total,
]);
