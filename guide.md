# TalkinCampus 校园匿名墙开发指南

仓库：https://github.com/lord-of-vaegirs/TalkinCampus

## 1. 项目范围

技术栈：HTML + CSS + JavaScript + PHP + MySQL。

必须遵守：

- 必须登录后才能发帖、评论、点赞、删除。
- 首页、帖子详情、评论区都不显示真实用户名。
- 不做用户搜索。
- 不做其他用户主页。
- 只能在个人中心查看自己的资料、帖子、评论。
- 只能删除自己发的帖子和评论。

## 2. 功能清单

| 模块 | 必须完成 |
| --- | --- |
| 账号 | 注册、登录、退出、获取当前用户 |
| 帖子 | 帖子列表、帖子详情、发布帖子、删除自己的帖子、搜索帖子、点赞/取消点赞 |
| 评论 | 评论列表、发布评论、删除自己的评论、点赞/取消点赞 |
| 个人中心 | 查看自己的资料、自己发过的帖子、自己发过的评论 |
| 匿名限制 | 不显示真实用户名、不提供用户搜索、不提供其他用户主页 |
| 安全 | 密码哈希、SQL 预处理、登录检查、作者身份检查、防重复点赞、防 XSS |

## 3. 三人分工

### 成员 A：前端

负责文件：

| 文件 | 任务 |
| --- | --- |
| `frontend/index.html` | 首页、搜索框、帖子列表、发布帖子入口 |
| `frontend/login.html` | 登录页 |
| `frontend/register.html` | 注册页 |
| `frontend/post.html` | 帖子详情、评论区 |
| `frontend/profile.html` | 个人中心 |
| `frontend/css/style.css` | 全站样式 |
| `frontend/js/api.js` | 封装 `fetch` |
| `frontend/js/auth.js` | 注册、登录、退出、登录状态 |
| `frontend/js/posts.js` | 帖子列表、详情、发布、删除、点赞 |
| `frontend/js/comments.js` | 评论发布、删除、点赞 |
| `frontend/js/search.js` | 帖子搜索 |
| `frontend/js/profile.js` | 个人中心 |

接口任务：

- 根据页面需要，先在 `frontend/js/api.js` 写好请求函数。
- 后端接口没完成时，可以先用假数据渲染页面。
- 页面需要新增字段时，先更新本指南的 API 表，再通知后端。

验收标准：

- 页面能正常跳转。
- 表单有非空检查。
- 按钮能调用后端接口。
- 使用 `textContent` 渲染用户输入。
- 首页、详情页、评论区不显示真实用户名。

### 成员 B：后端

负责文件：

| 文件 | 任务 |
| --- | --- |
| `backend/api/auth/register.php` | 注册 |
| `backend/api/auth/login.php` | 登录 |
| `backend/api/auth/logout.php` | 退出 |
| `backend/api/auth/me.php` | 获取当前用户 |
| `backend/api/posts/list.php` | 帖子列表 |
| `backend/api/posts/detail.php` | 帖子详情 |
| `backend/api/posts/create.php` | 发布帖子 |
| `backend/api/posts/delete.php` | 删除自己的帖子 |
| `backend/api/posts/toggle_like.php` | 帖子点赞/取消点赞 |
| `backend/api/comments/create.php` | 发布评论 |
| `backend/api/comments/delete.php` | 删除自己的评论 |
| `backend/api/comments/toggle_like.php` | 评论点赞/取消点赞 |
| `backend/api/search/search.php` | 搜索帖子 |
| `backend/api/users/me.php` | 个人中心数据 |
| `backend/includes/response.php` | 统一 JSON 返回 |
| `backend/includes/auth.php` | 登录检查 |
| `backend/includes/validators.php` | 输入检查 |

验收标准：

- 所有接口返回 JSON。
- 登录状态使用 PHP `session`。
- 数据库操作使用 PDO 预处理。
- 未登录不能发帖、评论、点赞、删除。
- 删除帖子前检查 `posts.user_id == 当前用户 id`。
- 删除评论前检查 `comments.user_id == 当前用户 id`。
- 接口输出字段必须和本指南 API 表一致。

统一返回：

```json
{
  "success": true,
  "message": "ok",
  "data": {}
}
```

### 成员 C：数据库、安全、集成

负责文件：

| 文件 | 任务 |
| --- | --- |
| `database/schema.sql` | 建表 SQL |
| `database/seed.sql` | 测试账号、测试帖子、测试评论 |
| `backend/config/database.php` | 数据库连接 |
| `README.md` | 本地运行说明 |

验收标准：

- `schema.sql` 能从空数据库建好所有表。
- `seed.sql` 能插入测试数据。
- 密码使用 `password_hash()`。
- 登录使用 `password_verify()`。
- 点赞表有唯一约束。
- 项目能按 README 在队友电脑上跑起来。

## 4. 接口协作方式

这个项目采用“页面驱动接口”的方式：

- 前端先按页面列出需要的接口和字段。
- 后端可以先写功能，也可以先写固定 JSON 占位输出。
- 接口没完成时，前端可以用假数据继续写页面。
- 接口字段一旦变化，必须同步修改本指南的 API 表。
- 联调时以后端真实接口为准，不保留前端假数据。

最小接口约定：

```json
{
  "success": true,
  "message": "ok",
  "data": {}
}
```

## 5. 项目目录

```text
TalkinCampus/
├── README.md
├── guide.md
├── frontend/
│   ├── index.html
│   ├── login.html
│   ├── register.html
│   ├── post.html
│   ├── profile.html
│   ├── css/style.css
│   └── js/
│       ├── api.js
│       ├── auth.js
│       ├── posts.js
│       ├── comments.js
│       ├── search.js
│       └── profile.js
├── backend/
│   ├── config/database.php
│   ├── includes/
│   │   ├── response.php
│   │   ├── auth.php
│   │   └── validators.php
│   └── api/
│       ├── auth/
│       ├── posts/
│       ├── comments/
│       ├── search/
│       └── users/
└── database/
    ├── schema.sql
    └── seed.sql
```

## 6. 数据库

必须建 5 张表：`users`、`posts`、`comments`、`post_likes`、`comment_likes`。

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nickname VARCHAR(50) NOT NULL,
  bio VARCHAR(255) DEFAULT '',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(100) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE post_likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_post_like (post_id, user_id),
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE comment_likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  comment_id INT NOT NULL,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_comment_like (comment_id, user_id),
  FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 7. API

前端按“页面”调用接口，后端按“路径”实现接口。

| 页面 | 方法 | 路径 | 功能 | 是否登录 |
| --- | --- | --- | --- | --- |
| 注册页 | POST | `/backend/api/auth/register.php` | 注册 | 否 |
| 登录页 | POST | `/backend/api/auth/login.php` | 登录 | 否 |
| 全站 | POST | `/backend/api/auth/logout.php` | 退出 | 是 |
| 全站 | GET | `/backend/api/auth/me.php` | 当前用户 | 是 |
| 首页 | GET | `/backend/api/posts/list.php` | 帖子列表 | 否 |
| 帖子详情 | GET | `/backend/api/posts/detail.php?id=1` | 帖子详情 | 否 |
| 首页 | POST | `/backend/api/posts/create.php` | 发帖 | 是 |
| 帖子详情 | POST | `/backend/api/posts/delete.php` | 删除自己的帖子 | 是 |
| 首页/详情 | POST | `/backend/api/posts/toggle_like.php` | 帖子点赞/取消 | 是 |
| 帖子详情 | POST | `/backend/api/comments/create.php` | 发评论 | 是 |
| 帖子详情 | POST | `/backend/api/comments/delete.php` | 删除自己的评论 | 是 |
| 帖子详情 | POST | `/backend/api/comments/toggle_like.php` | 评论点赞/取消 | 是 |
| 首页 | GET | `/backend/api/search/search.php?q=关键词` | 搜索帖子 | 否 |
| 个人中心 | GET | `/backend/api/users/me.php` | 个人中心 | 是 |

禁止做：

```text
/backend/api/users/public.php
/backend/api/users/search.php
```

## 8. 页面要求

| 页面 | 必须显示 | 不能显示 |
| --- | --- | --- |
| 首页 | 搜索框、帖子列表、点赞数、评论数、发布时间 | 作者用户名、用户主页入口 |
| 帖子详情 | 标题、内容、点赞按钮、评论列表、评论框 | 作者用户名、评论者用户名 |
| 登录页 | 用户名、密码、登录按钮 | 无 |
| 注册页 | 用户名、昵称、密码、注册按钮 | 无 |
| 个人中心 | 自己的资料、自己的帖子、自己的评论 | 别人的资料、别人的历史内容 |

## 9. 开发顺序

| 顺序 | 负责人 | 任务 |
| --- | --- | --- |
| 1 | C | 建目录、写 `schema.sql`、写 `database.php`、写 README 初稿 |
| 2 | A | 画出 5 个页面，写静态 HTML/CSS，列出每页需要的接口 |
| 3 | B | 写 `response.php`、`auth.php`，给每个接口先返回统一 JSON |
| 4 | A | 在 `api.js` 写请求函数；接口没好时先用假数据 |
| 5 | B | 实现注册、登录、退出、当前用户接口 |
| 6 | A+B | 联调注册和登录 |
| 7 | B | 实现帖子列表、详情、发布、删除、点赞接口 |
| 8 | A+B | 联调首页、发帖、帖子详情 |
| 9 | B | 实现评论、搜索、个人中心接口 |
| 10 | A+B | 联调评论、搜索、个人中心 |
| 11 | C | 加测试数据、安全检查、README 完整说明 |
| 12 | 全员 | 按最终验收清单逐项测试 |

## 10. GitHub 协作

分支：

* 主仓库
  - `main`：稳定版本
  - `develop`: 开发测试版本

* fork仓库
  - `feature/frontend`：成员 A
  - `feature/backend`：成员 B
  - `feature/database`：成员 C

规则：

- 不直接往 `main` 推未完成代码，PR先提交到主仓库的`develop`分支
- 一个功能完成后开 PR。
- PR 写清楚：改了什么、怎么测试。
- 至少一个队友看过再合并。
- 每天同步：完成了什么、卡在哪里、接口字段有没有变。
- 接口字段变化必须同步修改本指南。

提交信息举例：

```text
feat: add login page
feat: add register api
feat: add database schema
fix: prevent duplicate likes
docs: update readme
```

## 11. 最终验收

账号：

- 新用户能注册。
- 用户能登录。
- 用户能退出。
- 刷新后登录状态正常。

帖子：

- 首页能显示帖子。
- 登录用户能发帖。
- 未登录用户不能发帖。
- 作者能删除自己的帖子。
- 非作者不能删除别人的帖子。
- 搜索能找到标题或内容包含关键词的帖子。

评论：

- 登录用户能评论。
- 未登录用户不能评论。
- 评论作者能删除自己的评论。
- 非作者不能删除别人的评论。

点赞：

- 用户能点赞帖子，再点一次取消。
- 用户能点赞评论，再点一次取消。
- 同一用户不能重复点赞同一条内容。

匿名：

- 首页不显示真实用户名。
- 帖子详情不显示真实用户名。
- 评论区不显示真实用户名。
- 没有用户搜索。
- 没有其他用户主页。
- 不能通过接口查看其他用户历史内容。

安全：

- 密码不是明文。
- SQL 使用 PDO 预处理。
- 删除操作检查作者身份。
- 前端不用 `innerHTML` 渲染用户输入。
- 错误信息不暴露数据库细节。

文档：

- README 写清数据库创建方式。
- README 写清 PHP 启动方式。
- README 写清测试账号。
