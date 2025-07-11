# 🏫 Fontys ICT Server Setup Guide

## Setup for https://i888908.apollo.fontysict.net/fed-workshops

### 📋 Prerequisites
- Access to Fontys ICT server file system
- Database access on the Fontys server
- FTP/SFTP access or web-based file manager

### 🚀 Step-by-Step Installation

#### 1. Upload Files to Fontys Server
- Download the `php-version` folder
- Upload ALL files to your assigned directory:
  - **Path**: `public_html/fed-workshops/` (or your assigned directory)
  - **Important**: Upload the contents of `php-version/`, not the folder itself
  - **Structure**: The `index.php` should be directly in the `fed-workshops` directory

#### 2. Configure Environment
- Rename `.env.example` to `.env`
- Edit `.env` with your Fontys database details:
  ```env
  DB_HOST=localhost
  DB_NAME=your_fontys_database_name
  DB_USER=your_fontys_username
  DB_PASSWORD=your_fontys_password
  JWT_SECRET=your-random-32-character-secret-key
  DEBUG=false
  ```

#### 3. Create MySQL Database
**Using Fontys Database Management:**
- Create database: `fed_workshops` (or as assigned)
- Create user with all privileges
- Note credentials for `.env` file

#### 4. Run Database Setup
**Option A - Web Setup Wizard (Recommended):**
- Go to: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php`
- Follow the interactive setup wizard

**Option B - Manual phpMyAdmin:**
- Login to phpMyAdmin on Fontys server
- Select your database
- Import the file: `database/schema.sql`

#### 5. Test Your Installation
- **Test Path Configuration**: `https://i888908.apollo.fontysict.net/fed-workshops/test-paths.php`
- **Homepage**: `https://i888908.apollo.fontysict.net/fed-workshops/`
- **Login**: `https://i888908.apollo.fontysict.net/fed-workshops/login`

### 🔐 Default Login Accounts
- **Admin**: `admin@fed.nl` / `admin123`
- **Teacher**: `teacher@fed.nl` / `admin123`

**⚠️ IMPORTANT**: Change these passwords immediately after login!

### ✅ Expected Fontys Configuration

For `https://i888908.apollo.fontysict.net/fed-workshops/`, the system should detect:
- **Installation Type**: Subdirectory
- **Base Path**: `/fed-workshops/`
- **Asset URLs**: `/fed-workshops/assets/css/style.css`
- **API URLs**: `/fed-workshops/api/workshops`
- **Navigation**: All links work with `/fed-workshops/` prefix

### 🔧 File Structure on Fontys Server

Your directory should look like:
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

2. Use web migration: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php`

### 🆘 Troubleshooting

**Common Issues:**

1. **"Table not found" errors**:
   - **First**: Run database diagnostic: `https://i888908.apollo.fontysict.net/fed-workshops/database/diagnose.php`
   - **If tables are missing**: Use the setup wizard: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php`
   - **Manual fix**: Import `database/manual-setup.sql` via phpMyAdmin
   - **Don't create schema manually** - use the provided SQL file

2. **Database connection errors**:
   - Use Fontys-provided database credentials in `.env`
   - Ensure database user has proper privileges
   - Test connection using the diagnostic tool
   - Make sure database exists (create it if needed)

3. **Setup wizard fails**:
   - Check `.env` file has correct database settings
   - Ensure database exists and user has CREATE/INSERT privileges
   - Try manual setup via phpMyAdmin with `database/manual-setup.sql`

4. **404 Errors on routes**:
   - Ensure `.htaccess` file is uploaded with `RewriteBase /fed-workshops/`
   - Check if mod_rewrite is enabled on Fontys server
   - Verify file permissions (644 for files, 755 for directories)

5. **CSS/JS not loading**:
   - Check `test-paths.php` for correct asset URLs
   - Ensure `assets/` folder is uploaded
   - Verify paths show `/fed-workshops/assets/...`

6. **HTTPS issues**:
   - Fontys server should automatically handle HTTPS
   - Check mixed content warnings in browser console

### 🎯 Success Checklist for Fontys Server

- [ ] URL `https://i888908.apollo.fontysict.net/fed-workshops/` loads homepage
- [ ] All navigation links work correctly with `/fed-workshops/` prefix
- [ ] Login/register functions work
- [ ] CSS and styling loads properly from `/fed-workshops/assets/`
- [ ] Database connection successful
- [ ] Default passwords changed
- [ ] Test files deleted

### 📚 Fontys-Specific Notes

- **HTTPS**: The server should automatically redirect to HTTPS
- **File Permissions**: Usually handled automatically by the server
- **Database**: Use the database credentials provided by Fontys
- **Directory Structure**: Ensure files are in the correct subdirectory
- **Caching**: The server may cache files, so changes might take a moment to appear

---

**🎉 Your FED Workshop Planner is now live at https://i888908.apollo.fontysict.net/fed-workshops/**

### 📞 Support for Fontys Setup

If you encounter issues specific to the Fontys ICT server:
1. Check the `test-paths.php` file for path configuration
2. Verify the `.htaccess` file includes `RewriteBase /fed-workshops/`
3. Ensure database credentials match those provided by Fontys
4. Contact Fontys IT support for server-specific issues