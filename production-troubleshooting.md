# Production 500 Error Troubleshooting Guide

## Common Issues & Solutions

### 1. Laravel Error Logs
```bash
# Check latest errors
tail -n 100 storage/logs/laravel.log

# Clear logs if too large
> storage/logs/laravel.log
```

### 2. File Permissions (Most Common Issue)
```bash
# Set proper permissions
sudo chown -R www-data:www-data /path/to/your/project
sudo chmod -R 755 /path/to/your/project
sudo chmod -R 777 storage bootstrap/cache

# Alternative for shared hosting
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 3. Environment Configuration
```bash
# Check .env file
cat .env

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan cache:clear
php artisan view:clear
```

### 4. Database Issues
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check database credentials
php artisan config:cache
```

### 5. Composer Dependencies
```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Update autoloader
composer dump-autoload
```

### 6. PHP Extensions Required
- php-mbstring
- php-xml
- php-curl
- php-zip
- php-mysql (or appropriate DB driver)
- php-bcmath
- php-json

### 7. Web Server Configuration
**Apache (.htaccess):**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

**Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 8. Debug Mode in Production
```php
# .env file
APP_DEBUG=false  # Keep false in production
LOG_LEVEL=debug  # Increase logging
```

### 9. Common Account Creation Issues
- Missing database tables
- Incorrect database permissions
- File upload permissions
- Email configuration issues
- Validation rule failures

### 10. Quick Diagnostic Commands
```bash
# Laravel health check
php artisan about

# Check routes
php artisan route:list

# Test database
php artisan migrate:status

# Clear everything
php artisan optimize:clear
```

## Steps to Debug Account Creation 500 Error

1. **Check Error Logs**: Look for specific error messages
2. **Verify Database**: Ensure connection and tables exist
3. **Check Permissions**: Storage and cache directories
4. **Test Locally**: Reproduce with production .env settings
5. **Enable Debugging**: Temporarily set APP_DEBUG=true
6. **Check Web Server**: Error logs and configuration
7. **Verify Dependencies**: All required packages installed
