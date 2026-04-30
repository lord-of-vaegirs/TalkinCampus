USE talkincampus;

INSERT INTO users (id, username, password_hash, nickname, bio) VALUES
  (1, 'alice', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCibRKnD/DSCyrV6L6', 'Alice', '测试用户 Alice'),
  (2, 'bob', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCibRKnD/DSCyrV6L6', 'Bob', '测试用户 Bob'),
  (3, 'charlie', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCibRKnD/DSCyrV6L6', 'Charlie', '测试用户 Charlie');

INSERT INTO posts (id, user_id, title, content, created_at) VALUES
  (1, 1, '图书馆自习区推荐', '最近期末复习，大家觉得哪个自习区比较安静？', '2026-04-30 09:00:00'),
  (2, 2, '食堂新品讨论', '二食堂今天的新菜有人试过吗？味道怎么样？', '2026-04-30 10:30:00'),
  (3, 3, '校园跑打卡', '有没有晚上一起校园跑的同学？', '2026-04-30 12:00:00');

INSERT INTO comments (id, post_id, user_id, content, created_at) VALUES
  (1, 1, 2, '三楼靠窗位置还不错，但是下午人会多。', '2026-04-30 09:20:00'),
  (2, 1, 3, '建议早点去，晚饭后基本没座。', '2026-04-30 09:40:00'),
  (3, 2, 1, '我试了，偏辣，但是还可以。', '2026-04-30 10:45:00');

INSERT INTO post_likes (post_id, user_id) VALUES
  (1, 2),
  (1, 3),
  (2, 1);

INSERT INTO comment_likes (comment_id, user_id) VALUES
  (1, 1),
  (2, 1),
  (3, 2);
