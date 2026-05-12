# 数据库说明

本目录存放 TalkinCampus 的数据库相关文件。

## 文件说明

- `schema.sql`：创建 `talkincampus` 数据库、项目数据库用户和项目所需数据表。
- `seed.sql`：插入测试用户、测试帖子、测试评论和点赞数据。

建议 PHP 安装 `pdo_mysql` 和 `mbstring` 扩展；项目没有 `mbstring` 时可以运行，但中文长度校验会降级为字节长度。

## 初始化数据库

如果 Kali / Linux 上的 MySQL root 使用系统认证，推荐在项目根目录运行：

```bash
sudo mysql < database/schema.sql
mysql -u admin -p < database/seed.sql
```

如果你的 MySQL root 可以用密码登录，也可以运行：

```bash
mysql -u root -p < database/schema.sql
mysql -u admin -p < database/seed.sql
```

默认数据库名：

```text
talkincampus
```

项目数据库用户：

```text
用户名：admin
密码：admin
```

## 测试账号

```text
alice / password
bob / password
charlie / password
```

## 数据库连接配置

后端连接数据库时建议使用项目专用用户，不要使用 root：

```text
DB_HOST=localhost
DB_NAME=talkincampus
DB_USER=admin
DB_PASS=admin
```

如果后端使用环境变量，可以这样设置：

```bash
export DB_HOST=localhost
export DB_NAME=talkincampus
export DB_USER=admin
export DB_PASS=admin
```

## 注意

- `schema.sql` 会删除并重建项目表，运行前确认没有需要保留的数据。
- `admin / admin` 是开发环境数据库用户，只用于本课程项目本地开发。
