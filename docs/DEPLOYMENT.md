# Infinity CMS - Deployment Guide

## Quick Deployment Steps

### 1. Upload to Server

**Zip File Location:**

```
/Users/yumin/ventures/infinity-cms-deployment-YYYYMMDD-HHMMSS.zip
```

**Upload via:**

- FTP/SFTP to your web hosting
- SCP: `scp infinity-cms-deployment-*.zip user@server:/path/to/webroot/`
- cPanel File Manager

### 2. Extract on Server

```bash
# SSH into your server
ssh user@your-server.com

# Navigate to web root
cd /path/to/webroot

# Extract zip
unzip infinity-cms-deployment-*.zip

# Move files from subfolder to root (if needed)
mv infinity-cms/* .
mv infinity-cms/.htaccess .
mv infinity-cms/.env.php .
rm -rf infinity-cms

# Set permissions
chmod -R 755 .
chmod -R 777 storage/
chmod 644 .env.php
```

### 3. Configure Environment

**Edit `.env.php`:**

```php
'app' => [
    'url' => 'https://your-domain.com',  // Change this!
    'debug' => false,  // IMPORTANT: Set to false in production!
],

'database' => [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'your_database',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
],
```

### 4. Initialize Database

**Step 1: Create MySQL Database**

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE infinity_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user (optional, for better security)
CREATE USER 'cms_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON infinity_cms.* TO 'cms_user'@'localhost';
FLUSH PRIVILEGES;
exit;
```

**Step 2: Run Migrations**

1. Visit: `https://your-domain.com/admin/migrations`
2. Login with credentials
3. Click "Run Migrations"
4. Database tables created!

### 5. First Login

1. Visit: `https://your-domain.com/login`

2. **IMPORTANT: Change password immediately!**

### 6. Security Checklist

**✅ Production Checklist:**

- [ ] Set `debug => false` in `.env.php`
- [ ] Change default admin password
- [ ] Set proper file permissions (755 for folders, 644 for files)
- [ ] Set `storage/` to 777 (or 755 with proper ownership)
- [ ] Ensure `.env.php` is not publicly accessible
- [ ] Enable HTTPS (SSL certificate)
- [ ] Configure `session.secure => true` if using HTTPS
- [ ] Remove or secure `/setup` route (if implemented)
- [ ] Review `.htaccess` for security headers
- [ ] Set up regular backups for `database/` folder
- [ ] Change CSRF secret in `.env.php`

---

## Server Requirements

### Minimum Requirements:

- PHP 7.4 or higher
- MySQL 5.7+ (or MariaDB 10.2+)
- Apache with mod_rewrite OR Nginx
- 128MB RAM minimum
- 50MB disk space

### PHP Extensions Required:

- PDO (PHP Data Objects)
- pdo_mysql
- mbstring
- openssl
- session

### Apache Configuration:

```apache
# .htaccess already included
# Ensure mod_rewrite is enabled:
a2enmod rewrite
service apache2 restart
```

### Nginx Configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/infinity-cms/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Directory Structure

```
infinity-cms/
├── public/              ← Document root (point web server here)
│   ├── index.php       ← Front controller
│   └── .htaccess       ← Apache rewrite rules
├── app/                ← Application code
│   ├── Core/           ← Framework core
│   ├── Http/           ← Controllers & Middleware
│   └── Views/          ← Admin views
├── themes/             ← Public themes
│   └── infinity/       ← Default theme
├── database/           ← Database & migrations
│   └── migrations/     ← Migration files
├── storage/            ← Writable storage (777)
│   ├── cache/
│   ├── logs/
│   └── uploads/
├── config/             ← Configuration files
├── bootstrap/          ← Application bootstrap
└── .env.php           ← Environment config (SECURE THIS!)
```

---

## Post-Deployment Tasks

### 1. Test Core Functionality

- [ ] Homepage loads: `https://your-domain.com/`
- [ ] Login works: `https://your-domain.com/login`
- [ ] Admin dashboard accessible: `https://your-domain.com/admin/dashboard`
- [ ] Blog page works: `https://your-domain.com/blog`
- [ ] 404 page displays: `https://your-domain.com/nonexistent`

### 2. Configure Site Settings

1. Login to admin dashboard
2. Update site name in `.env.php`
3. Change admin password
4. Test migrations page
5. Review sample blog posts

### 3. Optional: Setup Cron Jobs

```bash
# Clear expired sessions daily
0 2 * * * find /path/to/infinity-cms/storage/sessions -type f -mtime +1 -delete

# Clear cache weekly
0 3 * * 0 rm -rf /path/to/infinity-cms/storage/cache/*

# Backup MySQL database daily
0 1 * * * mysqldump -u your_username -pyour_password your_database | gzip > /backups/db-$(date +\%Y\%m\%d).sql.gz
```

---

## Troubleshooting

### Issue: "Page Not Found" everywhere

**Solution:** Enable mod_rewrite or check Nginx config

```bash
# Apache:
sudo a2enmod rewrite
sudo service apache2 restart

# Check .htaccess exists in /public/
```

### Issue: "Database error" or blank page

**Solution:** Check MySQL connection and permissions

```bash
chmod 777 storage/

# Test MySQL connection
mysql -u your_username -p -h localhost your_database

# Check .env.php has correct MySQL credentials
```

### Issue: "500 Internal Server Error"

**Solution:** Check error logs and set `debug => true` temporarily

```bash
# Apache:
tail -f /var/log/apache2/error.log

# Nginx:
tail -f /var/log/nginx/error.log

# Application logs:
tail -f storage/logs/*.log
```

### Issue: Can't login

**Solution:** Run migrations to create admin user

```bash
# Visit: https://your-domain.com/admin/migrations
# Click "Run Migrations" to create all tables including users table
```

---

## Backup & Restore

### Backup

```bash
# Full backup
tar -czf infinity-cms-backup-$(date +%Y%m%d).tar.gz \
    infinity-cms/ \
    --exclude='infinity-cms/storage/cache' \
    --exclude='infinity-cms/storage/logs'

# MySQL database backup
mysqldump -u your_username -p your_database > backups/db-$(date +%Y%m%d).sql

# Or with compression
mysqldump -u your_username -p your_database | gzip > backups/db-$(date +%Y%m%d).sql.gz
```

### Restore

```bash
# Extract backup
tar -xzf infinity-cms-backup-YYYYMMDD.tar.gz

# Restore MySQL database
mysql -u your_username -p your_database < backups/db-YYYYMMDD.sql

# Or from compressed backup
gunzip < backups/db-YYYYMMDD.sql.gz | mysql -u your_username -p your_database
```

---

## Updating

### Update Process

1. Backup current installation and database
2. Extract new version to temporary folder
3. Copy `.env.php` from old to new
4. Copy `storage/uploads/` from old to new
5. Replace old files with new
6. Run new migrations: `/admin/migrations`
7. Test thoroughly

---

## Support & Documentation

- **Documentation:** See `docs/` folder
- **Security Guide:** `docs/SECURITY.md`
- **Admin Separation:** `docs/ADMIN-SEPARATION.md`
- **Infrastructure:** `docs/INFRASTRUCTURE.md`

---

## Production Optimization

### 1. Enable OPcache (PHP)

```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### 2. Enable Gzip Compression

```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### 3. Browser Caching

```apache
# .htaccess
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## Quick Reference

| Item                  | Default Value              |
| --------------------- | -------------------------- |
| **Admin URL**         | `/admin/dashboard`         |
| **Login URL**         | `/login`                   |
| **Migrations URL**    | `/admin/migrations`        |
| **Default Username**  | `admin`                    |
| **Default Password**  | `admin123`                 |
| **Database Type**     | MySQL 5.7+                 |
| **Document Root**     | `public/`                  |
| **Upload Directory**  | `storage/uploads/`         |

---

**Your Infinity CMS is now deployed!** 🚀

For questions or issues, check the documentation in the `docs/` folder.
