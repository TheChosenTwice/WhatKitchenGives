# What Kitchen Gives

A small PHP web application that recommends recipes based on the ingredients you have. This repository contains the app source, database migrations and a Docker setup to run the app locally.

# Quick install (Docker)

The project includes a Docker configuration in the `docker/` directory. The provided `docker-compose.yml` starts everything you need to run the app locally:

- Web server (Apache) with PHP 8.3
- MariaDB database
- Adminer for database administration

Steps:
1. Copy or review environment variables in `docker/.env` (database name, user, and password).
2. From the project root run `docker-compose up -d` inside the `docker` folder to start services.
3. The site will be available at: http://localhost/
4. Adminer (DB admin) is available at: http://localhost:8080/ (use credentials from `docker/.env`).

# Where to look in the code

- App logic: `App/Controllers`, `App/Models`, `App/Views`
- Framework helpers and bootstrap: `Framework/`
- SQL migrations: `db/migrations/`