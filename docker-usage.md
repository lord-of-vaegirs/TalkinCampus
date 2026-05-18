# TalkinCampus Docker 本地使用说明

这份文档用于在本机通过 Docker 启动 TalkinCampus 服务。项目已经把 PHP/Apache 后端和 MySQL 数据库打包进 Docker，不需要手动安装 PHP、Apache 或 MySQL。

## 1. 准备工作

请先确认本机已经安装并启动 Docker Desktop。

在终端进入项目根目录，也就是包含 `Dockerfile` 和 `docker-compose.yml` 的目录：

```bash
cd TalkinCampus
```

如果你的 Docker 版本较新，推荐使用 `docker compose` 命令；如果本机只支持旧版命令，可以把下面命令里的 `docker compose` 替换成 `docker-compose`。

## 2. 首次启动服务

在项目根目录执行：

```bash
docker compose up -d --build
```

这个命令会完成三件事：

- 构建 `app` 镜像，镜像内包含 PHP 8.2、Apache、MariaDB 和 `pdo_mysql` 扩展。
- 启动 `app` 容器，对外提供 Web 服务。
- 在同一个容器内初始化数据库，自动执行 `database/schema.sql` 和 `database/seed.sql`。

启动成功后，本机服务地址是：

```text
http://localhost:18083/frontend/index.html
```

## 3. 检查运行状态

查看容器是否正常运行：

```bash
docker compose ps
```

正常情况下应该看到：

- 只有一个 `app` 服务。
- `app` 状态为 `Up`，健康检查通过后会显示 `healthy`。

如果页面打不开或接口报错，可以查看日志：

```bash
docker compose logs --tail=200 app
```

## 4. 访问页面

浏览器打开以下地址：

```text
首页：http://localhost:18083/frontend/index.html
登录：http://localhost:18083/frontend/login.html
注册：http://localhost:18083/frontend/register.html
个人中心：http://localhost:18083/frontend/profile.html
```

帖子详情页一般通过首页帖子列表点击进入，也可以直接访问：

```text
http://localhost:18083/frontend/post.html?id=1
```

## 5. 测试账号

数据库初始化后会自动导入测试数据，可以直接使用以下账号登录：

```text
alice / password
bob / password
charlie / password
```

也可以在注册页创建新账号。新账号只保存在当前 Docker 数据库容器里。

## 6. 常用操作

暂停服务但保留当前容器数据：

```bash
docker compose stop
```

重新启动已经暂停的服务：

```bash
docker compose start
```

重启服务：

```bash
docker compose restart
```

停止并删除容器：

```bash
docker compose down
```

注意：当前 `docker-compose.yml` 只定义一个 `app` 服务，数据库也在这个容器内部运行。执行 `docker compose down` 会删除容器，数据库会在下次启动时重新初始化。日常只想临时关闭服务时，请优先使用 `docker compose stop`。

如果你修改了 `Dockerfile`、`docker-compose.yml`、后端依赖或初始化 SQL，建议重新构建：

```bash
docker compose up -d --build
```

如果你之前启动过旧版双容器配置，切换到当前单容器配置时可以清理遗留的 `db` 容器：

```bash
docker compose up -d --build --remove-orphans
```

如果想彻底恢复到初始测试数据，可以执行：

```bash
docker compose down
docker compose up -d --build
```

## 7. 服务配置说明

`docker-compose.yml` 中只启动一个服务：

```text
app：PHP 8.2 + Apache + MariaDB，映射到本机 http://localhost:18083
```

后端在同一容器内连接数据库时使用以下环境变量：

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

前端和后端代码在构建镜像时复制进容器：

```text
frontend -> /var/www/html/frontend
backend  -> /var/www/html/backend
```

因此修改本地 `frontend` 或 `backend` 目录下的代码后，需要重新执行 `docker compose up -d --build`。

## 8. 接口快速检查

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

## 9. 推荐验收流程

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

## 10. 常见问题

### 端口被占用

如果 `18083` 被占用，修改 `docker-compose.yml` 中 `app` 的端口映射：

```yaml
ports:
  - "8081:80"
```

然后重新启动：

```bash
docker compose up -d --build
```

访问地址也要改为：

```text
http://localhost:8081/frontend/index.html
```

### 数据库连接失败

按顺序检查：

```bash
docker compose ps
docker compose logs --tail=200 app
```

重点确认 `app` 是否已经变成 `healthy`，以及日志里是否出现数据库连接错误。

### 修改 SQL 后没有生效

当前项目只会在容器内数据库首次初始化时执行 `/docker-entrypoint-initdb.d` 里的脚本。重新创建容器即可重新导入：

```bash
docker compose down
docker compose up -d --build
```

### 进入容器内数据库

```bash
docker compose exec app mysql -uadmin -padmin talkincampus
```

### 查看容器环境变量

```bash
docker compose exec app env
```
