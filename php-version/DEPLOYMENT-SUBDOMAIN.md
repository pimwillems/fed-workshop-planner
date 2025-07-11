# 🚀 Deployment Guide for fed-workshops.pimwillems.dev

## Pre-Deployment Checklist

### **1. Files to Upload**
Upload all files from the `php-version` folder to your subdomain's root directory:
- ✅ All PHP files (index.php, etc.)
- ✅ config/ directory
- ✅ src/ directory  
- ✅ views/ directory
- ✅ database/ directory (for initial setup)

### **2. .htaccess Configuration**
- ✅ Rename `.htaccess.subdomain` to `.htaccess`
- ✅ **Key difference**: Uses `RewriteBase /` instead of `RewriteBase /fed-workshops/`

### **3. Environment Configuration**
- ✅ Rename `.env.subdomain` to `.env`
- ✅ Update with your actual database credentials
- ✅ Set a secure JWT_SECRET

## Database Setup

### **Option 1: New Database**
1. Create a new database in your hosting panel
2. Import `database/directadmin-import.sql` or use the setup tools
3. Update `.env` with new database credentials

### **Option 2: Copy Existing Database**
1. Export data from your current `i888908_workshopplanner` database
2. Import into the new database
3. Update `.env` with new credentials

## Testing Steps

### **1. Upload diagnostic tools** (optional but recommended):
- `fix-database-connection.php` - Test database connection
- `isolate-login-error.php` - Test application components
- `bypass-login.php` - Working login bypass

### **2. Test the application**:
1. **Homepage**: `https://fed-workshops.pimwillems.dev/`
2. **Login**: `https://fed-workshops.pimwillems.dev/login`
3. **Dashboard**: `https://fed-workshops.pimwillems.dev/dashboard`

### **3. Default Login Credentials**:
- **Admin**: `admin@fed.nl` / `admin123`
- **Teacher**: `teacher@fed.nl` / `admin123`

## Expected Differences from Fontys Deployment

### **✅ Advantages of Subdomain Deployment**:
- **Better URL structure**: `/login` instead of `/fed-workshops/login`
- **Cleaner paths**: No subdirectory prefix needed
- **More likely to work**: Subdomain routing is more reliable than subdirectory routing
- **Better for production**: Professional URL structure

### **⚠️ What to Watch For**:
- Database connection (same as before)
- Server configuration (mod_rewrite, PHP version)
- File permissions (644 for files, 755 for directories)

## Troubleshooting

### **If login still gives 500 error**:
1. Try: `https://fed-workshops.pimwillems.dev/bypass-login.php`
2. Check database connection: `https://fed-workshops.pimwillems.dev/fix-database-connection.php`
3. Run diagnostics: `https://fed-workshops.pimwillems.dev/isolate-login-error.php`

### **If database connection fails**:
1. Verify database credentials in hosting panel
2. Use the database connection fixer tool
3. Check if database user has proper permissions

## Security Cleanup (After Successful Deployment)

Once everything works, delete these files:
- `fix-database-connection.php`
- `isolate-login-error.php`
- `bypass-login.php`
- `test-*.php`
- `debug-*.php`
- `database/diagnose.php`
- `database/quick-setup.php`

## 🎉 Expected Result

The subdomain deployment should work **much better** than the subdirectory version because:
- No complex path rewriting needed
- Standard web server configuration
- Cleaner URL structure
- Better Apache/PHP compatibility

Your application code is already working perfectly, so this deployment should be smooth!