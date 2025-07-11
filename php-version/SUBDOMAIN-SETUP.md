# 🌐 Subdomain Setup Guide - fed-workshops.pimwillems.dev

## Quick Setup for Subdomain Installation

### 📋 Prerequisites
- Subdomain created: `fed-workshops.pimwillems.dev`
- PHP 7.4+ support on subdomain
- MySQL database access
- FTP/cPanel access to subdomain files

### 🚀 Step-by-Step Installation

#### 1. Create Subdomain (if not done)
**In cPanel:**
- Go to "Subdomains"
- Enter: `fed-workshops`
- Select domain: `pimwillems.dev`
- Document Root will be auto-created (e.g., `public_html/fed-workshops`)

#### 2. Upload Files to Subdomain Root
- Download the `php-version` folder
- Upload ALL files to your subdomain's document root:
  - **Path**: `public_html/fed-workshops/` (or wherever your subdomain points)
  - **Important**: Upload the contents of `php-version/`, not the folder itself
  - **Structure**: The `index.php` should be directly in the subdomain root

#### 3. Configure Environment
- Rename `.env.example` to `.env`
- Edit `.env` with your database details:
  ```env
  DB_HOST=localhost
  DB_NAME=fed_workshops
  DB_USER=your_database_username
  DB_PASSWORD=your_database_password
  JWT_SECRET=your-random-32-character-secret-key
  DEBUG=false
  ```

#### 4. Create MySQL Database
**In cPanel MySQL Databases:**
- Database name: `fed_workshops` (or similar)
- Create user with all privileges
- Note credentials for `.env` file

#### 5. Run Database Setup
**Option A - Web Setup Wizard (Recommended):**
- Go to: `https://fed-workshops.pimwillems.dev/database/web-migrate.php`
- Follow the interactive setup wizard

**Option B - Manual phpMyAdmin:**
- Login to phpMyAdmin
- Select your database
- Import the file: `database/schema.sql`

#### 6. Test Your Installation
- **Test Path Configuration**: `https://fed-workshops.pimwillems.dev/test-paths.php`
- **Homepage**: `https://fed-workshops.pimwillems.dev/`
- **Login**: `https://fed-workshops.pimwillems.dev/login`

### 🔐 Default Login Accounts
- **Admin**: `admin@fed.nl` / `admin123`
- **Teacher**: `teacher@fed.nl` / `admin123`

**⚠️ IMPORTANT**: Change these passwords immediately after login!

### ✅ Expected Subdomain Configuration

For `fed-workshops.pimwillems.dev`, the system should automatically detect:
- **Base Path**: `/` (root of subdomain)
- **Asset URLs**: `/assets/css/style.css`
- **API URLs**: `/api/workshops`
- **Navigation**: All links work without subdirectory prefixes

### 🔧 File Structure in Subdomain

Your subdomain directory should look like:
```
public_html/fed-workshops/
├── index.php
├── .env
├── .htaccess
├── config/
├── src/
├── views/
├── assets/
├── database/
└── [other files...]
```

### 🛡️ Security Post-Setup

1. **Delete test files**:
   - `test-paths.php`
   - `database/web-migrate.php` (after setup complete)

2. **Change default passwords**

3. **Set strong JWT secret in `.env`**

4. **Verify `.htaccess` is working** (pretty URLs should work)

### 🔄 Migrating Existing Data

If you have data from the Nuxt 3 version:

1. Add PostgreSQL connection to `.env`:
   ```env
   POSTGRES_HOST=your_render_host
   POSTGRES_PORT=5432
   POSTGRES_DB=workshop_planner
   POSTGRES_USER=your_postgres_user
   POSTGRES_PASSWORD=your_postgres_password
   ```

2. Use web migration: `https://fed-workshops.pimwillems.dev/database/web-migrate.php`

### 🆘 Troubleshooting

**Common Issues:**

1. **404 Errors on routes**:
   - Ensure `.htaccess` file is uploaded
   - Check if mod_rewrite is enabled
   - Verify file permissions (644 for files, 755 for directories)

2. **CSS/JS not loading**:
   - Check `test-paths.php` for correct asset URLs
   - Ensure `assets/` folder is uploaded
   - Verify web server can serve static files

3. **Database connection errors**:
   - Double-check `.env` database credentials
   - Ensure database user has proper privileges
   - Test connection using the web migration tool

4. **Subdomain not working**:
   - Wait for DNS propagation (can take up to 24 hours)
   - Check subdomain configuration in hosting panel
   - Verify subdomain points to correct directory

### 🎯 Success Checklist

- [ ] Subdomain `fed-workshops.pimwillems.dev` loads homepage
- [ ] All navigation links work correctly
- [ ] Login/register functions work
- [ ] CSS and styling loads properly
- [ ] Database connection successful
- [ ] Default passwords changed
- [ ] Test files deleted

---

**🎉 Your FED Workshop Planner is now live at https://fed-workshops.pimwillems.dev/**