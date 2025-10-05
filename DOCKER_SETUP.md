# Docker Setup Guide for Simple POS CI3

This guide will help you set up and run the Simple POS CI3 application using Docker and Docker Compose.

## 🐳 Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git (for cloning the repository)

### Install Docker (if not already installed)

**Windows:**
- Download and install Docker Desktop from [docker.com](https://www.docker.com/products/docker-desktop)

**macOS:**
- Download and install Docker Desktop from [docker.com](https://www.docker.com/products/docker-desktop)

**Linux (Ubuntu/Debian):**
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
```

## 🚀 Quick Start

### 1. Clone and Setup
```bash
# Clone the repository
git clone https://github.com/radityaIch/simple-pos-ci3
cd simple-pos-ci3

# Copy environment file (optional)
cp .env.example .env
```

### 2. Build and Run
```bash
# Build and start all services
docker-compose up -d --build

# View logs (optional)
docker-compose logs -f
```

### 3. Access the Application
- **Web Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Username: `root`
  - Password: `rootpassword`

### 4. Database Import
The database will be automatically imported when the MySQL container starts for the first time.

## 🛠️ Docker Services

| Service | Container Name | Port | Description |
|---------|---------------|------|-------------|
| app | simple_pos_app | 8080 | PHP 7.4 + Apache + Simple POS CI3 |
| db | simple_pos_db | 3306 | MySQL 5.7 Database |
| phpmyadmin | simple_pos_phpmyadmin | 8081 | Database Administration |

## 📋 Docker Commands

### Basic Operations
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart services
docker-compose restart

# View running containers
docker-compose ps

# View logs
docker-compose logs

# View logs for specific service
docker-compose logs app
docker-compose logs db
```

### Development Commands
```bash
# Rebuild containers (after code changes)
docker-compose up -d --build

# Execute commands in running container
docker-compose exec app bash
docker-compose exec db mysql -u root -p

# View real-time logs
docker-compose logs -f app
```

### Database Operations
```bash
# Access MySQL console
docker-compose exec db mysql -u root -p simple_pos_ci3

# Import SQL file manually
docker-compose exec -T db mysql -u root -prootpassword simple_pos_ci3 < databases/database_with_sample_data.sql

# Create database backup
docker-compose exec db mysqldump -u root -prootpassword simple_pos_ci3 > backup.sql

# Reset database (WARNING: This will delete all data)
docker-compose down
docker volume rm simple-pos-ci3_db_data
docker-compose up -d
```

## 🔧 Configuration

### Environment Variables
You can customize the setup by modifying the `.env` file or `docker-compose.yml`:

```env
# Application
BASE_URL=http://localhost:8080/

# Database
DB_HOST=db
DB_DATABASE=simple_pos_ci3
DB_USERNAME=root
DB_PASSWORD=rootpassword

# Ports (modify if needed)
APP_PORT=8080
DB_PORT=3306
PHPMYADMIN_PORT=8081
```

### Custom PHP Configuration
Modify `docker/php/conf.d/custom.ini` to change PHP settings:
```ini
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

## 🐛 Troubleshooting

### Common Issues

1. **Port Already in Use**
   ```bash
   # Check what's using the port
   netstat -tulpn | grep :8080
   
   # Change port in docker-compose.yml
   ports:
     - "8081:80"  # Change 8080 to 8081
   ```

2. **Database Connection Error**
   ```bash
   # Check if database is running
   docker-compose ps
   
   # Check database logs
   docker-compose logs db
   
   # Restart database service
   docker-compose restart db
   ```

3. **Permission Issues**
   ```bash
   # Fix file permissions
   docker-compose exec app chown -R www-data:www-data /var/www/html
   docker-compose exec app chmod -R 755 /var/www/html
   docker-compose exec app chmod -R 777 /var/www/html/application/cache
   docker-compose exec app chmod -R 777 /var/www/html/application/logs
   ```

4. **Application Not Loading**
   ```bash
   # Check Apache error logs
   docker-compose exec app tail -f /var/log/apache2/error.log
   
   # Check application logs
   docker-compose exec app tail -f /var/www/html/application/logs/log-*.php
   ```

### Reset Everything
```bash
# Stop and remove all containers, networks, and volumes
docker-compose down -v

# Remove all images
docker-compose down --rmi all

# Start fresh
docker-compose up -d --build
```

## 🔐 Production Deployment

### Security Considerations
1. **Change Default Passwords**
   ```yaml
   # In docker-compose.yml
   environment:
     MYSQL_ROOT_PASSWORD: your_strong_password_here
     MYSQL_PASSWORD: your_user_password_here
   ```

2. **Remove phpMyAdmin** (not needed in production)
   ```bash
   # Comment out or remove phpMyAdmin service in docker-compose.yml
   ```

3. **Use Environment Files**
   ```bash
   # Create production .env file
   cp .env.example .env.production
   # Edit .env.production with production values
   ```

4. **Enable HTTPS**
   - Use reverse proxy (nginx/Apache)
   - Configure SSL certificates
   - Update BASE_URL to https://

### Production docker-compose.yml
```yaml
version: '3.8'
services:
  app:
    build: .
    restart: always
    environment:
      - DB_HOST=db
      - DB_DATABASE=simple_pos_ci3
      - DB_USERNAME=pos_user
      - DB_PASSWORD=${DB_PASSWORD}
    depends_on:
      - db
    networks:
      - pos_network

  db:
    image: mysql:5.7
    restart: always
    environment:
      MYSQL_DATABASE: simple_pos_ci3
      MYSQL_USER: pos_user
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - pos_network

volumes:
  db_data:

networks:
  pos_network:
    driver: bridge
```

## 📊 Monitoring

### Container Health
```bash
# Check container status
docker-compose ps

# Check resource usage
docker stats

# View container details
docker-compose exec app ps aux
docker-compose exec db mysqladmin status -u root -p
```

### Logs
```bash
# Application logs
docker-compose logs app

# Database logs
docker-compose logs db

# Follow logs in real-time
docker-compose logs -f
```

## 🆘 Support

If you encounter any issues:

1. Check this troubleshooting guide
2. Review Docker and container logs
3. Ensure all prerequisites are installed
4. Check port availability
5. Verify file permissions

For additional help, please refer to the main README.md or create an issue in the repository.