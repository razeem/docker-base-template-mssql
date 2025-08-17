# Docker Base Template Composer Plugin

This project provides a **Composer plugin** that automatically copies a ready-to-use Docker configuration (including Dockerfiles, Compose files, and environment templates) into the root of your PHP project when you require this package. It is designed to help PHP/Drupal projects quickly bootstrap a local Docker-based development environment with best practices.

---

## Supported Variants

This template supports Ubuntu 22.04 for your Docker base image:

### Ubuntu Base Image (~22.04.0-rc1)

- Uses `ubuntu:22.04` as the base image.
- Suitable for projects that need more control over the OS environment or require additional non-PHP services.
- Installs PHP, Nginx, and other dependencies via `apt`, based on an external `apt-packages.env` file.
- Provides a more flexible and extensible environment for advanced use cases.

- You can specify the version you want to use when requiring the package:

```sh
  composer require --dev razeem/docker-base-template-mssql:~22.04.0-rc1
```

---

## Features

- **Automatic Docker Setup:**  
  On `composer install` or `composer update`, the plugin copies the `dist/` folder (containing Dockerfiles, Compose files, and config templates) into your project root.

- **Dynamic Project Naming:**  
  If a `project-code.txt` file exists in your project root, its contents are used to replace `project_name` and `project_folder` placeholders in `docker-compose.yml` and `docker-compose-vm.yml`.  
  If not, a random 3-character string is generated and used.

- **Environment Variable Management:**  
  Uses a generic `.env.dist` file for environment variables, which is to `.env` and customize according to your needs.

- **Extensible Docker Stack:**  
  - Includes configuration for PHP, Nginx, SSH, and other common services.  
  - Installs system packages based on an external `apt-packages.env` file (especially in the Ubuntu variant).

---

## Usage

### 1. Add the Plugin Repository

In the `repositories` section of your `composer.json`, add:

```json
{
    "type": "vcs",
    "url": "https://github.com/razeem/docker-base-template.git"
}
```

### 2. Require the Plugin

Specify a version:
```sh
composer require --dev razeem/docker-base-template:~22.04.0-rc1
```

### 3. On Install/Update

- The plugin will copy the contents of its `dist/` directory into your project root.
- Enter your project (Jira) code which will be used for naming the containers in local setup

### 4. Customize

- Edit `docker-compose.yml` and `docker-compose-vm.yml` as needed.
- Adjust `apt-packages.env` to add/remove system packages for your stack (especially for the Ubuntu variant).

---

## File Structure

- `dist/Dockerfile` – Main Docker build file, uses either Ubuntu or PHP-FPM as the base image depending on the version.
- `dist/docker-compose.yml` – Standard Docker Compose file.
- `dist/docker-compose-vm.yml` – Compose file for running in a VM.
- `dist/Docker/app/apt-packages.env` – List of system packages to install (one per line, mainly for Ubuntu variant).
- `dist/Docker/.env.dist` – Template for environment variables.
- `dist/Docker/ssh/sshd_config` – SSH server configuration.
- `dist/Docker/php/php.ini` – PHP configuration.
- `dist/Docker/nginx/nginx.conf` – Nginx configuration.
- `dist/Docker/scripts/start.sh` – Entrypoint/startup script.

---

## Customization

- **System Packages:**  
  Edit `Docker/app/apt-packages.env` to add/remove Ubuntu packages (for Ubuntu variant).

- **Environment Variables:**  
  Edit `.env` which is copied `Docker/.env.dist` for your local settings.

---

## Test Connectivity

- Mention your
`sqlcmd -S <project_name_database>,1433 -U SA -P 'reallyStrongPwd123' -Q "SELECT @@VERSION"`


---

## License

MIT

---

## Maintainer

- [Razeem Ahmad](https://www.drupal.org/u/razeem)

---

## Issues

Report bugs or request features at:  
[https://github.com/razeem/docker-base-template-mssql/issues](https://github.com/razeem/docker-base-template-mssql/issues)
