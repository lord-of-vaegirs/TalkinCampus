# TalkinCampus 接口文档

统一返回（所有接口）：

```json
{
  "success": true,
  "message": "ok",
  "data": {}
}
```

说明：

- 认证：使用 PHP `session`，请求需带上 Cookie；前端 `fetch` 使用 `credentials: "include"`。
- 匿名：除“当前用户/个人中心”外，接口禁止返回可识别他人的真实用户名等信息；首页/详情/评论列表不依赖用户名字段渲染。

## 字段约定

列表类：

- `data.items`：数组
- 可选分页：`data.page` `data.page_size` `data.total`

Post（帖子）：

```json
{
  "id": 1,
  "title": "标题",
  "content": "内容",
  "created_at": "2026-04-29 12:00:00",
  "like_count": 0,
  "comment_count": 0,
  "liked": false,
  "can_delete": false
}
```

Comment（评论）：

```json
{
  "id": 1,
  "post_id": 1,
  "content": "评论内容",
  "created_at": "2026-04-29 12:00:00",
  "like_count": 0,
  "liked": false,
  "can_delete": false
}
```

User（当前用户）：

```json
{
  "id": 1,
  "username": "test",
  "nickname": "测试用户",
  "bio": "",
  "created_at": "2026-04-29 12:00:00"
}
```

## 账号

### 注册

- 方法：POST
- 路径：`/backend/api/auth/register.php`
- 是否登录：否
- 请求（`application/x-www-form-urlencoded`）：
  - `username` string 必填
  - `password` string 必填
  - `nickname` string 必填
- 返回 `data`：

```json
{
  "user": { "id": 1, "username": "u", "nickname": "n", "bio": "", "created_at": "..." }
}
```

### 登录

- 方法：POST
- 路径：`/backend/api/auth/login.php`
- 是否登录：否
- 请求：
  - `username` string 必填
  - `password` string 必填
- 返回 `data`：

```json
{
  "user": { "id": 1, "username": "u", "nickname": "n", "bio": "", "created_at": "..." }
}
```

### 退出

- 方法：POST
- 路径：`/backend/api/auth/logout.php`
- 是否登录：是
- 请求：无
- 返回 `data`：`{}`

### 当前用户

- 方法：GET
- 路径：`/backend/api/auth/me.php`
- 是否登录：是
- 返回 `data`：

```json
{
  "user": { "id": 1, "username": "u", "nickname": "n", "bio": "", "created_at": "..." }
}
```

## 帖子

### 帖子列表

- 方法：GET
- 路径：`/backend/api/posts/list.php`
- 是否登录：否
- query（可选）：
  - `page` number
  - `page_size` number
- 返回 `data`：

```json
{
  "items": [],
  "page": 1,
  "page_size": 20,
  "total": 0
}
```

### 帖子详情（含评论列表）

- 方法：GET
- 路径：`/backend/api/posts/detail.php?id=1`
- 是否登录：否
- query：
  - `id` number 必填
- 返回 `data`：

```json
{
  "post": { "id": 1, "title": "t", "content": "c", "created_at": "...", "like_count": 0, "comment_count": 0, "liked": false, "can_delete": false },
  "comments": []
}
```

### 发布帖子

- 方法：POST
- 路径：`/backend/api/posts/create.php`
- 是否登录：是
- 请求：
  - `title` string 必填（<= 100）
  - `content` string 必填
- 返回 `data`：

```json
{ "id": 1 }
```

### 删除自己的帖子

- 方法：POST
- 路径：`/backend/api/posts/delete.php`
- 是否登录：是
- 请求：
  - `id` number 必填
- 返回 `data`：`{}`

### 帖子点赞/取消点赞

- 方法：POST
- 路径：`/backend/api/posts/toggle_like.php`
- 是否登录：是
- 请求：
  - `id` number 必填
- 返回 `data`：

```json
{ "liked": true, "like_count": 1 }
```

## 评论

### 发布评论

- 方法：POST
- 路径：`/backend/api/comments/create.php`
- 是否登录：是
- 请求：
  - `post_id` number 必填
  - `content` string 必填
- 返回 `data`：

```json
{ "id": 1 }
```

### 删除自己的评论

- 方法：POST
- 路径：`/backend/api/comments/delete.php`
- 是否登录：是
- 请求：
  - `id` number 必填
- 返回 `data`：`{}`

### 评论点赞/取消点赞

- 方法：POST
- 路径：`/backend/api/comments/toggle_like.php`
- 是否登录：是
- 请求：
  - `id` number 必填
- 返回 `data`：

```json
{ "liked": true, "like_count": 1 }
```

## 搜索

### 搜索帖子

- 方法：GET
- 路径：`/backend/api/search/search.php?q=关键词`
- 是否登录：否
- query：
  - `q` string 必填
  - `page` number 可选
  - `page_size` number 可选
- 返回 `data`：

```json
{
  "items": [],
  "page": 1,
  "page_size": 20,
  "total": 0
}
```

## 个人中心

### 个人中心数据（仅本人）

- 方法：GET
- 路径：`/backend/api/users/me.php`
- 是否登录：是
- 返回 `data`：

```json
{
  "user": { "id": 1, "username": "u", "nickname": "n", "bio": "", "created_at": "..." },
  "posts": [],
  "comments": []
}
```

### 个人统计数据

- 方法：GET
- 路径：`/backend/api/users/stats.php`
- 是否登录：是
- 返回 `data`：

```json
{
  "post_count": 10,
  "comment_count": 25,
  "total_likes": 50
}
```

说明：
- `post_count`: 用户发布的帖子总数
- `comment_count`: 用户发布的评论总数
- `total_likes`: 用户获得的总点赞数（帖子点赞 + 评论点赞）

## 禁止接口

```text
/backend/api/users/public.php
/backend/api/users/search.php
```
