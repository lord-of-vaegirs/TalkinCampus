# TalkinCampus Docker 本地使用说明

这份文档用于在本机通过 Docker 启动 TalkinCampus 服务。项目已经把 PHP/Apache 后端、MariaDB 数据库、数据库连接环境变量和初始化逻辑全部写入 `Dockerfile`，不需要手动安装 PHP、Apache 或 MySQL，也不需要单独启动数据库容器。

## 1. 准备工作

请先确认本机已经安装并启动 Docker Desktop。

在终端进入项目根目录，也就是包含 `Dockerfile` 的目录：

```bash
cd TalkinCampus
```

## 2. 构建镜像

在项目根目录执行：

```bash
docker build -t talkincampus .
```

这个命令会完成三件事：

- 根据 `Dockerfile` 构建镜像。
- 将 PHP 8.2、Apache、MariaDB 和 `pdo_mysql` 扩展打包进同一个镜像。
- 将前端、后端和数据库初始化 SQL 复制进镜像。

## 3. 启动容器

前台启动：

```bash
docker run --rm -p 18083:80 talkincampus
```

容器内部 Apache 监听 `80` 端口，本机通过 `18083` 访问。MariaDB 会在同一个容器内启动，首次启动时会自动执行：

```text
database/schema.sql
database/seed.sql
```

如果需要后台运行：

```bash
docker run -d --name talkincampus -p 18083:80 talkincampus
```

启动成功后，本机服务地址是：

```text
http://localhost:18083/
```

## 4. 检查运行状态

查看容器是否正常运行：

```bash
docker ps --filter name=talkincampus
```

正常情况下应该看到：

- 只有一个 TalkinCampus 容器。
- 容器状态为 `Up`。

如果页面打不开或接口报错，可以查看日志：

```bash
docker logs --tail=200 talkincampus
```

如果你使用前台 `docker run --rm ...` 启动，日志会直接显示在当前终端里。

## 5. 访问页面

浏览器打开以下地址：

```text
根目录：http://localhost:18083/
首页：http://localhost:18083/frontend/index.html
登录：http://localhost:18083/frontend/login.html
注册：http://localhost:18083/frontend/register.html
个人中心：http://localhost:18083/frontend/profile.html
```

访问根目录 `/` 会自动进入首页 `/frontend/index.html`。

帖子详情页一般通过首页帖子列表点击进入，也可以直接访问：

```text
http://localhost:18083/frontend/post.html?id=1
```

## 6. 测试账号

数据库初始化后会自动导入测试数据，可以直接使用以下账号登录：

```text
alice / password
bob / password
charlie / password
```

也可以在注册页创建新账号。新账号只保存在当前 Docker 容器内部数据库里。

## 7. 常用操作

如果使用前台方式启动：

```bash
docker run --rm -p 18083:80 talkincampus
```

按 `Ctrl+C` 即可停止容器，并且 `--rm` 会自动删除容器。

如果使用后台方式启动：

```bash
docker run -d --name talkincampus -p 18083:80 talkincampus
```

查看日志：

```bash
docker logs --tail=200 talkincampus
```

停止并删除容器：

```bash
docker stop talkincampus
docker rm talkincampus
```

注意：当前项目没有配置持久化数据卷。删除容器后，数据库会在下次启动时重新初始化。

如果你修改了 `Dockerfile`、前端、后端或初始化 SQL，建议重新构建：

```bash
docker build -t talkincampus .
```

## 8. 服务配置说明

`Dockerfile` 会构建一个完整应用镜像：

```text
PHP 8.2 + Apache + MariaDB + pdo_mysql
```

镜像内默认环境变量：

```text
DB_HOST=127.0.0.1
DB_NAME=talkincampus
DB_USER=admin
DB_PASS=admin
```

数据库初始化文件位于：

```text
database/schema.sql
database/seed.sql
```

前端、后端和根目录入口页在构建镜像时复制进容器：

```text
index.html -> /var/www/html/index.html
frontend -> /var/www/html/frontend
backend  -> /var/www/html/backend
```

因此修改本地 `frontend` 或 `backend` 目录下的代码后，需要重新执行 `docker build -t talkincampus .`。

## 9. 接口快速检查

可以在浏览器开发者工具的 Network 面板查看接口请求，也可以直接访问以下 GET 接口：

```text
http://localhost:18083/backend/api/posts/list.php
http://localhost:18083/backend/api/posts/detail.php?id=1
http://localhost:18083/backend/api/search/search.php?q=图书馆
http://localhost:18083/backend/api/auth/me.php
http://localhost:18083/backend/api/users/me.php
http://localhost:18083/backend/api/users/stats.php
```

需要登录态或表单数据的接口建议通过页面操作测试：

```text
POST /backend/api/auth/login.php
POST /backend/api/auth/register.php
POST /backend/api/auth/logout.php
POST /backend/api/posts/create.php
POST /backend/api/posts/delete.php
POST /backend/api/posts/toggle_like.php
POST /backend/api/comments/create.php
POST /backend/api/comments/delete.php
POST /backend/api/comments/toggle_like.php
```

接口统一返回 JSON，格式类似：

```json
{
  "success": true,
  "message": "ok",
  "data": {}
}
```

## 10. 推荐验收流程

账号功能：

1. 打开登录页。
2. 使用 `alice / password` 登录。
3. 刷新首页，确认仍保持登录状态。
4. 点击退出，确认回到未登录状态。
5. 打开注册页，注册一个新账号。

帖子功能：

1. 登录后打开首页。
2. 发布一条测试帖子。
3. 确认新帖子出现在首页列表。
4. 点击帖子进入详情页。
5. 确认自己发布的帖子可以删除，其他人的帖子不能删除。

评论功能：

1. 在帖子详情页输入评论并提交。
2. 确认评论出现在评论列表。
3. 确认自己的评论可以删除。
4. 换另一个账号登录，确认不能删除别人的评论。

点赞功能：

1. 对帖子点击推荐。
2. 确认推荐数增加。
3. 再点一次，确认取消推荐。
4. 对评论执行同样操作。

搜索和个人中心：

1. 在首页搜索框输入关键词。
2. 确认能搜索出标题或内容包含关键词的帖子。
3. 打开个人中心，确认能看到自己的资料、帖子和评论。
4. 确认首页侧边栏能显示帖子数、评论数、获赞数。

## 11. 常见问题

### 端口被占用

如果 `18083` 被占用，修改 `docker run` 命令左侧的宿主机端口即可：

```bash
docker run --rm -p 8081:80 talkincampus
```

访问地址也要改为：

```text
http://localhost:8081/
```

### 根目录 Forbidden

当前镜像已经内置 `/var/www/html/index.html`，访问 `http://localhost:18083/` 会自动进入 `/frontend/index.html`。如果仍然看到 Forbidden，请确认镜像已经重新构建，而不是运行旧镜像。

### 数据库连接失败

按顺序检查：

```bash
docker ps --filter name=talkincampus
docker logs --tail=200 talkincampus
```

重点确认容器是否处于 `Up` 状态，以及日志里是否出现数据库连接错误。

### 修改 SQL 后没有生效

当前项目只会在容器内数据库首次初始化时执行 `/docker-entrypoint-initdb.d` 里的脚本。重新创建容器即可重新导入：

```bash
docker stop talkincampus
docker rm talkincampus
docker build -t talkincampus .
docker run -d --name talkincampus -p 18083:80 talkincampus
```

### 进入容器内数据库

```bash
docker exec -it talkincampus mysql -uadmin -padmin talkincampus
```

### 查看容器环境变量

```bash
docker exec talkincampus env
```
