# TalkinCampus

语言 / Language：**中文** | [English](README.en.md)

TalkinCampus 是一个面向校园场景的匿名讨论墙 Web 应用。当前版本为 **Version 1.0**，支持学生在匿名展示身份的前提下发布帖子、评论、点赞、搜索内容，并在个人中心管理自己的内容。

## 版本状态

```text
当前版本：Version 1.0
技术栈：HTML + CSS + JavaScript + PHP + MySQL
部署方式：Docker Compose 或普通 PHP/MySQL Web 服务
```

## 主要功能

账号功能：

- 注册、登录、退出。
- 使用 PHP Session 保持登录状态。
- 密码使用哈希存储，登录时通过 `password_verify()` 校验。

帖子功能：

- 查看帖子列表和帖子详情。
- 登录后发布帖子。
- 只能删除自己发布的帖子。
- 支持帖子点赞和取消点赞。
- 支持按关键词搜索帖子标题和内容。

评论功能：

- 在帖子详情页查看评论。
- 登录后发布评论。
- 只能删除自己发布的评论。
- 支持评论点赞和取消点赞。

个人中心：

- 查看自己的账号资料。
- 查看自己发布过的帖子和评论。
- 查看个人统计数据，包括发帖数、评论数和获赞数。

匿名与安全限制：

- 首页、帖子详情和评论列表不展示真实用户名。
- 不提供用户搜索接口。
- 不提供其他用户主页。
- 数据库操作使用 PDO 预处理。
- 点赞表使用唯一约束，防止重复点赞。
- 前端渲染用户输入时使用安全的文本渲染方式，降低 XSS 风险。

## 项目结构

```text
TalkinCampus/
├── Dockerfile
├── docker-compose.yml
├── docker-usage.md
├── api.md
├── frontend/
│   ├── index.html
│   ├── login.html
│   ├── register.html
│   ├── post.html
│   ├── profile.html
│   ├── css/style.css
│   └── js/
├── backend/
│   ├── config/database.php
│   ├── includes/
│   └── api/
│       ├── auth/
│       ├── posts/
│       ├── comments/
│       ├── search/
│       └── users/
└── database/
    ├── schema.sql
    ├── seed.sql
    └── README.md
```

## 方式一：使用 Docker 本地启动

Docker 是最推荐的本地启动方式。项目的 `Dockerfile` 已经把 PHP/Apache 服务、MariaDB 数据库、数据库默认环境变量和初始化逻辑都放进同一个镜像中，不需要手动安装 PHP、Apache 或 MySQL，也不需要单独启动数据库容器。

### 1. 准备环境

请先安装并启动 Docker Desktop。

进入项目根目录：

```bash
cd TalkinCampus
```

### 2. 构建镜像

```bash
docker build -t talkincampus .
```

该命令会根据 `Dockerfile` 构建一个完整镜像，镜像内包含：

```text
PHP 8.2 + Apache + MariaDB + pdo_mysql
frontend/
backend/
database/schema.sql
database/seed.sql
```

### 3. 启动容器

```bash
docker run --rm -p 18083:80 talkincampus
```

该命令会启动一个容器。容器内部 Web 服务监听 `80` 端口，本机使用 `18083` 访问。数据库会在容器内自动启动，并首次自动执行：

```text
database/schema.sql
database/seed.sql
```

### 4. 访问项目

浏览器打开：

```text
http://localhost:18083/
```

常用页面：

```text
根目录：http://localhost:18083/
首页：http://localhost:18083/frontend/index.html
登录：http://localhost:18083/frontend/login.html
注册：http://localhost:18083/frontend/register.html
个人中心：http://localhost:18083/frontend/profile.html
```

访问根目录 `/` 会自动进入首页 `/frontend/index.html`。

### 5. 测试账号

```text
alice / password
bob / password
charlie / password
```

### 6. 常用 Docker 命令

如果使用 `docker run --rm` 前台运行，按 `Ctrl+C` 即可停止并删除容器。

如果希望后台运行，可以执行：

```bash
docker run -d --name talkincampus -p 18083:80 talkincampus
```

查看日志：

```bash
docker logs --tail=200 talkincampus
```

停止并删除后台容器：

```bash
docker stop talkincampus
docker rm talkincampus
```

更完整的 Docker 使用说明见 [docker-usage.md](docker-usage.md)。

## 方式二：普通 PHP/MySQL 本地部署

这种方式适合不用 Docker、希望直接使用本机 PHP 和 MySQL 的场景。

### 1. 环境要求

需要本机已经安装：

```text
PHP 8.x
PHP PDO MySQL 扩展
PHP mbstring 扩展（建议安装，用于更准确地处理中文长度）
MySQL 8.x 或兼容版本
```

如果使用 Apache 或 Nginx，还需要确保 Web 服务可以解析 PHP。

### 2. 初始化数据库

在项目根目录执行：

```bash
mysql -u root -p < database/schema.sql
mysql -u admin -p < database/seed.sql
```

如果你的 MySQL root 使用系统认证，例如部分 Linux/Kali 环境，可以使用：

```bash
sudo mysql < database/schema.sql
mysql -u admin -p < database/seed.sql
```

默认数据库信息：

```text
数据库名：talkincampus
项目数据库用户：admin
项目数据库密码：admin
```

### 3. 配置后端数据库连接

后端默认会读取环境变量：

```text
DB_HOST
DB_NAME
DB_USER
DB_PASS
```

如果没有设置环境变量，会使用以下默认值：

```text
DB_HOST=localhost
DB_NAME=talkincampus
DB_USER=admin
DB_PASS=admin
```

本地普通部署通常不需要额外修改配置。数据库连接文件位于：

```text
backend/config/database.php
```

### 4. 使用 PHP 内置服务器启动

这是最轻量的普通本地启动方式。在项目根目录执行：

```bash
php -S localhost:18083
```

然后访问：

```text
http://localhost:18083/frontend/index.html
```

注意：前端请求路径使用 `/backend/...`，因此前端页面和后端接口需要从同一个站点根目录访问。不要直接双击打开 `frontend/index.html`，否则接口路径和 Session Cookie 可能无法正常工作。

### 5. 使用 Apache 或 Nginx 部署

也可以把整个项目目录作为 Web 站点根目录，例如：

```text
/var/www/html/TalkinCampus
```

站点的 DocumentRoot 应指向项目根目录，也就是包含 `frontend/` 和 `backend/` 的目录。确保浏览器可以同时访问：

```text
/frontend/index.html
/backend/api/posts/list.php
```

访问地址类似：

```text
http://localhost/frontend/index.html
```

部署时请保持 `frontend` 和 `backend` 在同一个站点根目录下。

如果本机需要同时部署多个类似项目，可以把不同项目放在 `/var/www/html/` 下的不同子目录，例如：

```text
/var/www/html/TalkinCampus
/var/www/html/OtherCampus
```

然后访问：

```text
http://localhost/TalkinCampus/frontend/index.html
```

前端会根据当前页面路径自动把接口请求转换为同一项目目录下的后端路径，例如：

```text
/TalkinCampus/backend/api/posts/list.php
```

因此不要直接访问 `http://localhost/TalkinCampus/frontend/` 目录本身；如果服务器没有配置目录默认页，可能会出现 403 或 404。请访问明确的页面文件，例如 `/frontend/index.html`。

## 接口说明

接口统一返回 JSON：

```json
{
  "success": true,
  "message": "ok",
  "data": {}
}
```

常用接口：

```text
GET  /backend/api/posts/list.php
GET  /backend/api/posts/detail.php?id=1
GET  /backend/api/search/search.php?q=图书馆
GET  /backend/api/auth/me.php
GET  /backend/api/users/me.php
GET  /backend/api/users/stats.php
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

完整接口文档见 [api.md](api.md)。

## 推荐项目测试流程（开发人员注意）

1. 使用测试账号 `alice / password` 登录。
2. 刷新首页，确认登录状态仍然存在。
3. 发布一条帖子，并确认帖子出现在首页列表。
4. 进入帖子详情页，发布评论。
5. 对帖子和评论分别点赞，再次点击确认取消点赞。
6. 搜索帖子标题或内容中的关键词。
7. 打开个人中心，确认资料、自己的帖子、自己的评论和统计数据正常显示。
8. 尝试删除自己的帖子或评论，确认可以删除。
9. 换另一个账号登录，确认不能删除别人的帖子或评论。
10. 检查首页、帖子详情和评论列表，确认不显示真实用户名。

## 开发说明

- 前端代码位于 `frontend/`。
- 后端接口位于 `backend/api/`。
- 通用后端工具函数位于 `backend/includes/`。
- 数据库结构和测试数据位于 `database/`。
- Docker 镜像构建和容器启动逻辑位于 `Dockerfile`。

修改前端或后端代码后，Docker 部署通常刷新浏览器即可看到效果，因为 `frontend` 和 `backend` 目录通过 volume 挂载到了容器中。修改数据库初始化 SQL 后，需要重新初始化数据库。
