# TalkinCampus

Language: [中文](README.md) | **English**

TalkinCampus is an anonymous campus discussion wall web application. The current release is **Version 1.0**. It allows students to publish posts, comment, like content, search posts, and manage their own content in the profile page while keeping identities anonymous in public views.

## Version Status

```text
Current version: Version 1.0
Tech stack: HTML + CSS + JavaScript + PHP + MySQL
Deployment options: Docker Compose or a regular PHP/MySQL web server
```

## Features

Account features:

- Register, log in, and log out.
- Keep login state with PHP Session.
- Store passwords as hashes and verify login with `password_verify()`.

Post features:

- View the post list and post detail pages.
- Publish posts after logging in.
- Delete only posts created by the current user.
- Like and unlike posts.
- Search posts by title or content keyword.

Comment features:

- View comments on the post detail page.
- Publish comments after logging in.
- Delete only comments created by the current user.
- Like and unlike comments.

Profile features:

- View the current user's profile.
- View posts and comments created by the current user.
- View user statistics, including post count, comment count, and total received likes.

Anonymity and security restrictions:

- The home page, post detail page, and comment list do not display real usernames.
- No user search API is provided.
- No public profile pages for other users are provided.
- Database operations use PDO prepared statements.
- Like tables use unique constraints to prevent duplicate likes.
- The frontend renders user input as text to reduce XSS risk.

## Project Structure

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

## Option 1: Run Locally with Docker

Docker is the recommended local startup method. It starts both the PHP/Apache application service and the MySQL database, so you do not need to install PHP, Apache, or MySQL manually.

### 1. Prepare the Environment

Install and start Docker Desktop first.

Enter the project root directory:

```bash
cd TalkinCampus
```

If your Docker version is older, replace `docker compose` with `docker-compose` in the commands below.

### 2. Start the Services

```bash
docker compose up -d --build
```

This command starts two containers:

```text
app: PHP 8.2 + Apache, exposed on local port 8080
db: MySQL 8.0, exposed on local port 3306
```

The database automatically runs:

```text
database/schema.sql
database/seed.sql
```

### 3. Open the Application

Open this URL in your browser:

```text
http://localhost:8080/frontend/index.html
```

Common pages:

```text
Home:    http://localhost:8080/frontend/index.html
Login:   http://localhost:8080/frontend/login.html
Sign up: http://localhost:8080/frontend/register.html
Profile: http://localhost:8080/frontend/profile.html
```

### 4. Test Accounts

```text
alice / password
bob / password
charlie / password
```

### 5. Common Docker Commands

Check service status:

```bash
docker compose ps
```

View logs:

```bash
docker compose logs --tail=200 app db
```

Stop services while keeping current container data:

```bash
docker compose stop
```

Start stopped services:

```bash
docker compose start
```

Stop and remove containers:

```bash
docker compose down
```

The current `docker-compose.yml` does not configure a separate MySQL data volume. After `docker compose down`, the database container is removed, and the database will be initialized again on the next startup. See [docker-usage.md](docker-usage.md) for the full Docker guide.

## Option 2: Regular PHP/MySQL Local Deployment

Use this method if you do not want to use Docker and prefer running the project directly with local PHP and MySQL.

### 1. Requirements

Install the following locally:

```text
PHP 8.x
PHP PDO MySQL extension
MySQL 8.x or a compatible version
```

If you use Apache or Nginx, make sure the web server can execute PHP files.

### 2. Initialize the Database

Run these commands from the project root:

```bash
mysql -u root -p < database/schema.sql
mysql -u admin -p < database/seed.sql
```

If your MySQL root account uses system authentication, as in some Linux/Kali environments, use:

```bash
sudo mysql < database/schema.sql
mysql -u admin -p < database/seed.sql
```

Default database information:

```text
Database name: talkincampus
Project database user: admin
Project database password: admin
```

### 3. Configure Backend Database Connection

The backend reads these environment variables:

```text
DB_HOST
DB_NAME
DB_USER
DB_PASS
```

If no environment variables are set, it uses these defaults:

```text
DB_HOST=127.0.0.1
DB_NAME=talkincampus
DB_USER=admin
DB_PASS=admin
```

For regular local deployment, no extra configuration is usually required. The database connection file is:

```text
backend/config/database.php
```

### 4. Start with PHP Built-in Server

This is the simplest regular local startup method. Run this command from the project root:

```bash
php -S localhost:8080
```

Then open:

```text
http://localhost:8080/frontend/index.html
```

Note: the frontend uses `/backend/...` as the API path, so frontend pages and backend APIs must be served from the same site root. Do not open `frontend/index.html` directly by double-clicking it, otherwise API paths and Session cookies may not work correctly.

### 5. Deploy with Apache or Nginx

You can also use the whole project directory as the web site root, for example:

```text
/var/www/html/TalkinCampus
```

The site's DocumentRoot should point to the project root, the directory containing `frontend/` and `backend/`. Make sure the browser can access both:

```text
/frontend/index.html
/backend/api/posts/list.php
```

The URL should look like:

```text
http://localhost/frontend/index.html
```

Keep `frontend` and `backend` under the same site root.

If you need to deploy multiple similar projects on the same local server, place them under different subdirectories in `/var/www/html/`, for example:

```text
/var/www/html/TalkinCampus
/var/www/html/OtherCampus
```

Then visit:

```text
http://localhost/TalkinCampus/frontend/index.html
```

The frontend automatically maps API requests to the backend path under the same project directory, for example:

```text
/TalkinCampus/backend/api/posts/list.php
```

Do not visit the directory URL `http://localhost/TalkinCampus/frontend/` directly. If the web server has no directory index configured, it may return 403 or 404. Visit an explicit page file such as `/frontend/index.html`.

## API Overview

All APIs return JSON:

```json
{
  "success": true,
  "message": "ok",
  "data": {}
}
```

Common APIs:

```text
GET  /backend/api/posts/list.php
GET  /backend/api/posts/detail.php?id=1
GET  /backend/api/search/search.php?q=library
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

See [api.md](api.md) for the full API documentation.

## Recommended Test Flow for Developers

1. Log in with `alice / password`.
2. Refresh the home page and confirm the login state is preserved.
3. Publish a post and confirm it appears in the home page list.
4. Open the post detail page and publish a comment.
5. Like a post and a comment, then click again to confirm unlike works.
6. Search for a keyword in post titles or content.
7. Open the profile page and confirm profile data, owned posts, owned comments, and statistics are displayed correctly.
8. Delete your own post or comment and confirm deletion works.
9. Log in with another account and confirm you cannot delete other users' posts or comments.
10. Check the home page, post detail page, and comment list to confirm real usernames are not displayed.

## Development Notes

- Frontend code is in `frontend/`.
- Backend APIs are in `backend/api/`.
- Shared backend helpers are in `backend/includes/`.
- Database schema and seed data are in `database/`.
- Docker configuration is in `Dockerfile` and `docker-compose.yml`.

After changing frontend or backend code, Docker deployment usually only needs a browser refresh because `frontend` and `backend` are mounted into the container as volumes. After changing database initialization SQL, reinitialize the database.
