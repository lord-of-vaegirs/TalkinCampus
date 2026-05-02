<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/common.php';

require_method('GET');

$user = get_current_user_or_fail($pdo);
$userId = (int) $user['id'];

$postsSql = '
    SELECT
        p.id,
        p.title,
        p.content,
        p.created_at,
        COALESCE(pl.like_count, 0) AS like_count,
        COALESCE(cc.comment_count, 0) AS comment_count,
        CASE WHEN vpl.user_id IS NULL THEN 0 ELSE 1 END AS liked,
        1 AS can_delete
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
       AND vpl.user_id = :viewer_id
    WHERE p.user_id = :user_id
    ORDER BY p.created_at DESC, p.id DESC
';

$postsStmt = $pdo->prepare($postsSql);
$postsStmt->bindValue(':viewer_id', $userId, PDO::PARAM_INT);
$postsStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$postsStmt->execute();
$posts = array_map('build_post_payload', $postsStmt->fetchAll());

$commentsSql = '
    SELECT
        c.id,
        c.post_id,
        c.content,
        c.created_at,
        COALESCE(cl.like_count, 0) AS like_count,
        CASE WHEN vcl.user_id IS NULL THEN 0 ELSE 1 END AS liked,
        1 AS can_delete
    FROM comments c
    LEFT JOIN (
        SELECT comment_id, COUNT(*) AS like_count
        FROM comment_likes
        GROUP BY comment_id
    ) cl ON cl.comment_id = c.id
    LEFT JOIN comment_likes vcl
        ON vcl.comment_id = c.id
       AND vcl.user_id = :viewer_id
    WHERE c.user_id = :user_id
    ORDER BY c.created_at DESC, c.id DESC
';

$commentsStmt = $pdo->prepare($commentsSql);
$commentsStmt->bindValue(':viewer_id', $userId, PDO::PARAM_INT);
$commentsStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$commentsStmt->execute();
$comments = array_map('build_comment_payload', $commentsStmt->fetchAll());

respond_success([
    'user' => build_user_payload($user),
    'posts' => $posts,
    'comments' => $comments,
]);
