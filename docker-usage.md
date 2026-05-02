# TalkinCampus Docker 启动与测试流程

## 1. 前置条件

- 已安装 Docker Desktop
- 当前项目目录：`d:\Code\web\co-work\TalkinCampus`

## 2. 首次启动

在项目根目录执行：

```bash
docker-compose down -v
docker-compose up -d --build
```

说明：

- `--build` 会重新构建 `app` 镜像，安装 PHP 的 `pdo_mysql` 扩展
- `down -v` 会删除旧容器和数据库卷，确保 MySQL 重新执行 `database/schema.sql` 与 `database/seed.sql`

## 3. 检查容器状态

执行：

```bash
docker-compose ps
```

正常情况下应看到：

- `db` 容器状态为 `healthy`
- `app` 容器状态为 `Up`

如果需要查看日志：

```bash
docker-compose logs --tail=200 app db
```

## 4. 打开项目

浏览器访问：

- 首页：`http://localhost:8080/frontend/index.html`
- 登录页：`http://localhost:8080/frontend/login.html`
- 注册页：`http://localhost:8080/frontend/register.html`
- 个人中心：`http://localhost:8080/frontend/profile.html`

## 5. 测试账号

数据库初始化后会自动导入测试数据，可以直接登录：

```text
alice / password
bob / password
charlie / password
```

## 6. 推荐测试顺序

### 账号

1. 打开登录页
2. 使用 `alice / password` 登录
3. 刷新首页，确认仍保持登录状态
4. 点击退出，确认回到未登录状态
5. 再进入注册页，注册一个新账号确认注册功能正常

### 帖子

1. 登录后打开首页
2. 确认首页出现发帖框
3. 发布一个测试帖子
4. 确认新帖子出现在首页列表
5. 点击帖子进入详情页
6. 确认自己发布的帖子可以删除，其他人的帖子不能删除

### 评论

1. 在帖子详情页输入评论并提交
2. 确认评论出现在评论列表
3. 确认自己的评论可以删除
4. 换另一个账号登录，确认不能删除别人的评论

### 点赞

1. 对帖子点击“推荐”
2. 确认推荐数增加
3. 再点一次确认取消推荐
4. 对评论执行同样操作

### 搜索

1. 在首页右侧输入关键词
2. 确认能搜索出标题或内容包含关键词的帖子

### 个人中心

1. 打开 `http://localhost:8080/frontend/profile.html`
2. 确认能看到自己的资料
3. 确认能看到自己的帖子和评论
4. 确认首页右侧显示帖子数、评论数、获赞数

## 7. 匿名要求检查

需要重点确认以下内容：

- 首页不显示真实用户名
- 帖子详情不显示真实用户名
- 评论列表不显示真实用户名
- 没有用户搜索接口
- 没有其他用户主页

## 8. 接口快速检查

可以直接在浏览器开发者工具的 `Network` 面板中查看请求结果，或直接访问以下接口：

```text
GET  /backend/api/posts/list.php
GET  /backend/api/posts/detail.php?id=1
GET  /backend/api/search/search.php?q=图书馆
GET  /backend/api/auth/me.php
GET  /backend/api/users/me.php
GET  /backend/api/users/stats.php
POST /backend/api/auth/login.php
POST /backend/api/auth/register.php
POST /backend/api/posts/create.php
POST /backend/api/comments/create.php
```

统一返回格式：

```json
{
  "success": true,
  "message": "ok",
  "data": {}
}
```

## 9. 常见问题

### 数据库连接失败

请按顺序检查：

1. 是否使用了最新命令重新构建：

```bash
docker-compose down -v
docker-compose up -d --build
```

2. `db` 是否已经 `healthy`：

```bash
docker-compose ps
```

3. `app` 日志里是否还有报错：

```bash
docker-compose logs --tail=200 app
```

4. `db` 日志里是否成功执行了初始化 SQL：

```bash
docker-compose logs --tail=200 db
```

### 修改了 SQL 但没有生效

MySQL 官方镜像只会在“首次初始化数据库”时执行 `docker-entrypoint-initdb.d` 里的脚本。

如果你改了 `database/schema.sql` 或 `database/seed.sql`，需要重新初始化：

```bash
docker-compose down -v
docker-compose up -d --build
```

### 查看容器内环境变量

```bash
docker-compose exec app env
```

### 进入数据库容器

```bash
docker-compose exec db mysql -uroot -pyour_password talkincampus
```
