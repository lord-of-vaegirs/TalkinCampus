<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('GET');

$postId = get_query_int('id', '帖子 id');
$viewerId = get_current_user_id() ?? 0;

$postSql = '
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
    WHERE p.id = :post_id
    LIMIT 1
';

$postStmt = $pdo->prepare($postSql);
$postStmt->bindValue(':viewer_id', $viewerId, PDO::PARAM_INT);
$postStmt->bindValue(':viewer_like_user_id', $viewerId, PDO::PARAM_INT);
$postStmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
$postStmt->execute();
$post = $postStmt->fetch();

if (!$post) {
    respond_not_found('帖子不存在');
}

$commentsSql = '
    SELECT
        c.id,
        c.post_id,
        c.content,
        c.created_at,
        COALESCE(cl.like_count, 0) AS like_count,
        CASE WHEN vcl.user_id IS NULL THEN 0 ELSE 1 END AS liked,
        CASE WHEN c.user_id = :viewer_id THEN 1 ELSE 0 END AS can_delete
    FROM comments c
    LEFT JOIN (
        SELECT comment_id, COUNT(*) AS like_count
        FROM comment_likes
        GROUP BY comment_id
    ) cl ON cl.comment_id = c.id
    LEFT JOIN comment_likes vcl
        ON vcl.comment_id = c.id
       AND vcl.user_id = :viewer_like_user_id
    WHERE c.post_id = :post_id
    ORDER BY c.created_at ASC, c.id ASC
';

$commentsStmt = $pdo->prepare($commentsSql);
$commentsStmt->bindValue(':viewer_id', $viewerId, PDO::PARAM_INT);
$commentsStmt->bindValue(':viewer_like_user_id', $viewerId, PDO::PARAM_INT);
$commentsStmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
$commentsStmt->execute();

$comments = array_map('build_comment_payload', $commentsStmt->fetchAll());

respond_success([
    'post' => build_post_payload($post),
    'comments' => $comments,
]);
