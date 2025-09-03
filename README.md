# 🐳 Docker Base Template Composer Plugin (MSSQL)

A **Composer plugin** that bootstraps a ready-to-use Docker configuration (Dockerfiles, Compose files, and environment templates) into your PHP project.  
Ideal for PHP/Drupal projects needing a quick, opinionated Docker setup with MSSQL support.

---

## 🌍 Supported Variant

### Ubuntu Base (~22.04.0)
- Base image: `ubuntu:22.04`
- Installs PHP, Nginx, and dependencies via `apt` (driven by `apt-packages.env`)
- Flexible, extensible environment for advanced use cases

📦 Install: `composer require --dev razeem/docker-base-template-mssql:~22.04.2`

---

## ✨ Features
- ⚡ **Automatic Docker Setup:** Copies Dockerfiles, Compose files, and templates on `composer install/update`
- 🏷 **Dynamic Naming:** Generates project folder and service names automatically
- ⚙️ **Configurable:**  
  - `apt-packages.env` for system packages  
  - `.env.dist` → `.env` for environment variables  
  - Configs for PHP, Nginx, SSH, and MSSQL included  

---

## 🚀 Usage

### 1. Add Repository in `composer.json`
``` json
{
    "type": "vcs",
    "url": "https://github.com/razeem/docker-base-template-mssql.git"
}
```

### 2. Require the Plugin
`composer require --dev razeem/docker-base-template-mssql:~22.04.2`

### 3. Configure
- Copy `.env.dist` → `.env` and customize  
- Adjust `docker-compose.yml` and `docker-compose-vm.yml` as needed  
- Add/remove Ubuntu packages in `apt-packages.env`  

---

## 📂 File Structure
- `dist/Dockerfile` – Base Docker build  
- `dist/docker-compose.yml` – Standard compose  
- `dist/docker-compose-vm.yml` – VM-specific compose  
- `dist/Docker/app/apt-packages.env` – Package list  
- `dist/Docker/.env.dist` – Env template  
- `dist/Docker/php/php.ini` – PHP config  
- `dist/Docker/nginx/nginx.conf` – Nginx config  
- `dist/Docker/ssh/sshd_config` – SSH config  
- `dist/Docker/scripts/start.sh` – Entrypoint  

---

## 🔧 Customization
- **System Packages:** Update `Docker/app/apt-packages.env`  
- **Environment Variables:** Copy `.env.dist` → `.env` and set local values  

---

## 🔌 Test Connectivity
Check MSSQL connection:

`sqlcmd -S <project_name_database>,1433 -U SA -P 'reallyStrongPwd123' -Q "SELECT @@VERSION"`  

---

## 📜 License
MIT  

---

## 👤 Maintainer
[Razeem Ahmad](https://www.drupal.org/u/razeem)  

---

## 🐞 Issues
Report at 👉 [GitHub Issues](https://github.com/razeem/docker-base-template-mssql/issues)
