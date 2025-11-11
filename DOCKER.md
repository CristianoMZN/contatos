# Docker and Deployment Guide

Complete guide for Docker infrastructure and CI/CD deployment of the Contatos application.

## Table of Contents

- [Quick Start](#quick-start)
- [Architecture Overview](#architecture-overview)
- [Development Setup](#development-setup)
- [Production Deployment](#production-deployment)
- [Configuration](#configuration)
- [PHP-FPM Tuning](#php-fpm-tuning)
- [CI/CD Workflows](#cicd-workflows)
- [Troubleshooting](#troubleshooting)

## Quick Start

### Local Development

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Edit .env with your settings (optional for dev)
nano .env

# 3. Start development environment
docker-compose up -d

# 4. Access the application
# - App: http://localhost:8080
# - Adminer (DB tool): http://localhost:8081

# 5. View logs
docker-compose logs -f app

# 6. Stop environment
docker-compose down
```

### Production Deployment

See [Production Deployment](#production-deployment) section.

## Architecture Overview

### Services

The application uses a multi-container architecture:

```
┌─────────────────────────────────────────┐
│         Nginx + PHP-FPM (app)           │
│  - PHP 8.4 with extensions              │
│  - Nginx web server                     │
│  - Supervisord for process management   │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│         MariaDB (db)                    │
│  - Version 11.2                         │
│  - Persistent data storage              │
└─────────────────────────────────────────┘

Development Only:
┌─────────────────────────────────────────┐
│         Adminer (adminer)               │
│  - Database management UI               │
└─────────────────────────────────────────┘
```

### Container Structure

```
app container:
├── Nginx (port 8080)
├── PHP-FPM (Unix socket)
└── Supervisord (process manager)
```

### Volumes

- `db-data`: MariaDB data persistence
- `uploads-data`: User uploaded files
- `storage-data`: Application cache and sessions

## Development Setup

### Prerequisites

- Docker 20.10+
- Docker Compose 2.0+

### Development Features

The development environment includes:

- **Live code reloading**: Source code mounted as volume
- **Xdebug**: Debugging support on port 9003
- **Adminer**: Database management UI
- **Error display**: Full error reporting enabled
- **No Opcache validation**: Immediate code changes

### Starting Development Environment

```bash
# Build and start services
docker-compose up -d --build

# Or use the override file explicitly
docker-compose -f docker-compose.yml -f docker-compose.override.yml up -d --build
```

### Useful Development Commands

```bash
# Access app container shell
docker-compose exec app bash

# Run migrations
docker-compose exec app php vendor/bin/phinx migrate

# Install new dependencies
docker-compose exec app composer require vendor/package

# View app logs
docker-compose logs -f app

# Restart app service
docker-compose restart app

# Rebuild after Dockerfile changes
docker-compose up -d --build app
```

### Xdebug Configuration

Configure your IDE to connect to Xdebug on port 9003:

**VSCode (launch.json):**
```json
{
  "name": "Listen for Xdebug (Docker)",
  "type": "php",
  "request": "launch",
  "port": 9003,
  "pathMappings": {
    "/var/www/html": "${workspaceFolder}"
  }
}
```

**PhpStorm:**
1. Settings → PHP → Servers
2. Name: `localhost`
3. Host: `localhost`
4. Port: `8080`
5. Map project root to `/var/www/html`

## Production Deployment

### Prerequisites on Production Server

1. **Rocky Linux 8/9** (or RHEL-compatible)
2. **Docker** and **Docker Compose** installed
3. **SSH access** configured
4. **Domain** pointed to server IP

### Manual Production Deployment

```bash
# 1. On production server, create deployment directory
ssh user@production-server
mkdir -p ~/contatos-deploy
cd ~/contatos-deploy

# 2. Copy docker-compose files
# Transfer: docker-compose.yml, docker-compose.override.prod.yml

# 3. Create .env file
cp .env.example .env
nano .env  # Configure production values

# 4. Login to container registry
docker login ghcr.io -u YOUR_USERNAME

# 5. Pull and start services
docker-compose -f docker-compose.yml -f docker-compose.override.prod.yml pull
docker-compose -f docker-compose.yml -f docker-compose.override.prod.yml up -d

# 6. Verify deployment
docker-compose -f docker-compose.yml -f docker-compose.override.prod.yml ps
curl http://localhost:8080/health

# 7. View logs
docker-compose -f docker-compose.yml -f docker-compose.override.prod.yml logs -f
```

### Automated CI/CD Deployment

The project includes GitHub Actions workflows for automated deployment.

#### Required GitHub Secrets

Configure these in: `Settings → Secrets and variables → Actions`

**Secrets:**
- `DEPLOY_HOST`: Production server IP/hostname
- `DEPLOY_USER`: SSH username
- `DEPLOY_SSH_KEY`: SSH private key for authentication
- `DEPLOY_PORT`: SSH port (optional, default: 22)
- `REGISTRY_USERNAME`: Container registry username
- `REGISTRY_PASSWORD`: Container registry password/token

**Variables:**
- `REGISTRY_URL`: Container registry URL (default: `ghcr.io`)
- `REGISTRY_NAMESPACE`: Registry namespace (default: repository owner)
- `IMAGE_NAME`: Image name (default: `contatos`)
- `PHP_VERSION`: PHP version to build (default: `8.4`)

#### Deployment Process

1. **Automatic**: Push to `main`/`master` triggers build → deploy
2. **Manual**: Go to Actions → Deploy to Production → Run workflow

## Configuration

### Environment Variables

#### Application Settings

| Variable | Description | Default | Example |
|----------|-------------|---------|---------|
| `APP_ENV` | Environment name | `production` | `production`, `staging`, `development` |
| `APP_DEBUG` | Enable debug mode | `false` | `true`, `false` |
| `APP_URL` | Application URL | `http://localhost` | `https://contatos.example.com` |
| `APP_PORT` | External port | `8080` | `8080`, `80`, `443` |

#### Database Settings

| Variable | Description | Default | Example |
|----------|-------------|---------|---------|
| `DB_HOST` | Database host | `db` | `db`, `mysql.example.com` |
| `DB_PORT` | Database port | `3306` | `3306` |
| `DB_DATABASE` | Database name | `contatos` | `contatos` |
| `DB_USERNAME` | Database user | `contatos` | `contatos` |
| `DB_PASSWORD` | Database password | - | `secure_password` |
| `DB_ROOT_PASSWORD` | Root password | - | `root_password` |

#### Container Registry

| Variable | Description | Default | Example |
|----------|-------------|---------|---------|
| `REGISTRY_URL` | Registry URL | `ghcr.io` | `ghcr.io`, `docker.io`, `registry.example.com` |
| `REGISTRY_NAMESPACE` | Namespace/org | Repository owner | `cristianomzn`, `myorg` |
| `IMAGE_NAME` | Image name | `contatos` | `contatos`, `contact-app` |
| `IMAGE_TAG` | Image tag | `latest` | `latest`, `v1.0.0`, `main-abc123` |

## PHP-FPM Tuning

### Process Manager Modes

#### 1. Dynamic (Recommended for Production)

Best for: Variable load, balanced resource usage

```env
PHP_FPM_PM=dynamic
PHP_FPM_MAX_CHILDREN=50
PHP_FPM_START_SERVERS=5
PHP_FPM_MIN_SPARE_SERVERS=5
PHP_FPM_MAX_SPARE_SERVERS=35
PHP_FPM_MAX_REQUESTS=1000
```

**How it works:**
- Starts with 5 processes
- Scales between 5-35 idle processes
- Maximum 50 total processes
- Each process handles 1000 requests before respawn

#### 2. OnDemand (For Low Traffic)

Best for: Low traffic, minimal memory usage

```env
PHP_FPM_PM=ondemand
PHP_FPM_MAX_CHILDREN=20
PHP_FPM_PROCESS_IDLE_TIMEOUT=10
```

**How it works:**
- Spawns processes only when needed
- Kills idle processes after 10s
- Minimal memory footprint

#### 3. Static (For High Consistent Load)

Best for: Predictable high traffic

```env
PHP_FPM_PM=static
PHP_FPM_MAX_CHILDREN=100
```

**How it works:**
- Always maintains 100 processes
- Highest performance, highest memory usage
- No overhead from spawning

### Memory Calculation

**Formula:**
```
Required RAM = MAX_CHILDREN × Memory per Process
```

**Typical PHP-FPM process:** 30-50MB

**Examples:**
- 20 children × 50MB = 1GB RAM
- 50 children × 50MB = 2.5GB RAM
- 100 children × 50MB = 5GB RAM

### Tuning Recommendations

#### Small Server (2GB RAM)
```env
PHP_FPM_PM=ondemand
PHP_FPM_MAX_CHILDREN=20
```

#### Medium Server (4GB RAM)
```env
PHP_FPM_PM=dynamic
PHP_FPM_MAX_CHILDREN=50
PHP_FPM_START_SERVERS=5
PHP_FPM_MIN_SPARE_SERVERS=5
PHP_FPM_MAX_SPARE_SERVERS=35
```

#### Large Server (8GB+ RAM)
```env
PHP_FPM_PM=dynamic
PHP_FPM_MAX_CHILDREN=100
PHP_FPM_START_SERVERS=10
PHP_FPM_MIN_SPARE_SERVERS=10
PHP_FPM_MAX_SPARE_SERVERS=50
```

### Monitoring PHP-FPM

```bash
# Check FPM status
docker-compose exec app curl http://localhost:8080/fpm-status

# Check FPM ping
docker-compose exec app curl http://localhost:8080/fpm-ping

# View FPM logs
docker-compose logs -f app | grep php-fpm
```

## CI/CD Workflows

### Build Workflow

**Trigger:** Push to main/master, tags, PRs

**Actions:**
1. Checkout code
2. Set up Docker Buildx
3. Login to registry
4. Build multi-arch image (amd64, arm64)
5. Push to registry

**Outputs:** Docker image with tags

### Deploy Workflow

**Trigger:** Manual dispatch, successful build

**Actions:**
1. Create deployment package
2. Transfer files via SSH/SCP
3. Login to registry on server
4. Pull latest images
5. Restart containers
6. Verify health
7. Rollback on failure

**Safety features:**
- Automatic rollback on failure
- Health check verification
- Environment backup

### Customizing Workflows

#### Change PHP Version

**Option 1: Via workflow dispatch**
```
Actions → Build and Push Docker Image → Run workflow
→ Select PHP version
```

**Option 2: Via repository variable**
```
Settings → Variables → PHP_VERSION = 8.3
```

**Option 3: Update Dockerfile**
```dockerfile
FROM php:8.3-fpm-alpine AS base
```

#### Use Custom Registry

**Update variables/secrets:**
```env
REGISTRY_URL=registry.example.com
REGISTRY_USERNAME=your-username
REGISTRY_PASSWORD=your-password
```

## Troubleshooting

### Container won't start

```bash
# Check logs
docker-compose logs app

# Common issues:
# 1. Port already in use
lsof -i :8080  # Find process using port

# 2. Permission denied
sudo chown -R 1000:1000 storage/ uploads/

# 3. Database connection failed
docker-compose logs db
```

### Application errors

```bash
# View application logs
docker-compose exec app tail -f storage/logs/app.log

# View PHP errors
docker-compose exec app tail -f /var/log/php-error.log

# View Nginx errors
docker-compose exec app tail -f /var/log/nginx/error.log
```

### Performance issues

```bash
# Check resource usage
docker stats

# Check FPM pool status
docker-compose exec app curl http://localhost:8080/fpm-status

# Adjust PHP-FPM settings in .env
# Increase MAX_CHILDREN if all processes busy
# Decrease if using too much memory
```

### Database issues

```bash
# Access database
docker-compose exec db mysql -u root -p

# Or use Adminer (dev only)
# http://localhost:8081

# Backup database
docker-compose exec db mysqldump -u root -p contatos > backup.sql

# Restore database
docker-compose exec -T db mysql -u root -p contatos < backup.sql
```

### Deployment failures

```bash
# On production server
cd ~/contatos-deploy

# Check deployment logs
cat deploy.log

# Manual rollback
docker-compose -f docker-compose.yml -f docker-compose.override.prod.yml down
# Restore .env.backup
docker-compose -f docker-compose.yml -f docker-compose.override.prod.yml up -d
```

### Registry authentication

```bash
# GitHub Container Registry (GHCR)
echo $GITHUB_TOKEN | docker login ghcr.io -u USERNAME --password-stdin

# Docker Hub
docker login -u USERNAME

# Private registry
docker login registry.example.com -u USERNAME
```

## Advanced Topics

### Multi-Process Architecture

For background workers or schedulers, use separate containers:

**docker-compose.override.prod.yml:**
```yaml
services:
  worker:
    image: ${REGISTRY_URL}/${REGISTRY_NAMESPACE}/${IMAGE_NAME}:${IMAGE_TAG}
    command: php worker.php
    depends_on:
      - app
      - db
    environment:
      # Same as app
```

### SSL/TLS Termination

Use a reverse proxy (Traefik, Nginx Proxy, Caddy):

**Example with Nginx Proxy:**
```yaml
services:
  app:
    environment:
      VIRTUAL_HOST: contatos.example.com
      LETSENCRYPT_HOST: contatos.example.com
      LETSENCRYPT_EMAIL: admin@example.com
```

### Monitoring

Consider adding:
- Prometheus + Grafana for metrics
- ELK stack for log aggregation
- Sentry for error tracking

### Backup Strategy

```bash
# Automated backup script
#!/bin/bash
docker-compose exec -T db mysqldump -u root -p$DB_ROOT_PASSWORD contatos | \
  gzip > "backup-$(date +%Y%m%d-%H%M%S).sql.gz"

# Upload to S3/backup service
aws s3 cp backup-*.sql.gz s3://your-bucket/backups/
```

## Security Best Practices

1. **Use secrets management**: Don't commit `.env` files
2. **Regular updates**: Keep base images updated
3. **Non-root user**: Container runs as `www` user
4. **Minimize attack surface**: Only expose necessary ports
5. **Network isolation**: Use Docker networks
6. **Scan images**: Use `docker scan` or Trivy
7. **Strong passwords**: Generate secure database passwords
8. **HTTPS only**: Use TLS termination in production
9. **Regular backups**: Automate database backups
10. **Monitoring**: Set up alerts for anomalies

## Support

For issues or questions:
1. Check [Troubleshooting](#troubleshooting) section
2. Review container logs
3. Open an issue on GitHub
4. Consult Docker/PHP-FPM documentation

---

**Last Updated:** 2025-11-11
**Docker Version:** 20.10+
**PHP Version:** 8.4
**Compose Version:** 2.0+
