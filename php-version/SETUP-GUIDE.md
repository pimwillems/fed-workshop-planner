# 🚀 Workshop Planner PHP - Setup Guide

## Quick Setup (No Command Line Required)

### 📋 Prerequisites
- Web hosting with PHP 7.4+ and MySQL
- cPanel or File Manager access
- phpMyAdmin access

### 🔧 Step-by-Step Setup

#### 1. Upload Files
- Download the `php-version` folder
- Upload ALL files to your web hosting directory:
  - **For Fontys ICT server**: Upload to `public_html/fed-workshops/` (or your assigned directory)
  - **For subdomain**: Upload to the subdomain's root directory (e.g., `public_html/` for the subdomain)
  - **For subdirectory**: Upload to `public_html/workshopplanner/`
  - **Other hosts**: Upload to the appropriate web root directory

#### 2. Configure Environment
- Rename `.env.example` to `.env`
- Edit `.env` with your database details:
  ```env
  DB_HOST=localhost
  DB_NAME=workshop_planner
  DB_USER=your_database_username
  DB_PASSWORD=your_database_password
  JWT_SECRET=create-a-random-32-character-string
  DEBUG=false
  ```

#### 3. Create Database
**Using cPanel:**
- Go to cPanel → MySQL Databases
- Create database: `workshop_planner`
- Create user with all privileges
- Add user to database

#### 4. Setup Database Schema
**Easy Method (Recommended):**
- **For Fontys ICT server**: Go to: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php`
- **For subdomain**: Go to: `http://fed-workshops.yourdomain.dev/database/web-migrate.php`
- **For subdirectory**: Go to: `http://yourdomain.com/workshopplanner/database/web-migrate.php`
- Follow the setup wizard

**Manual Method:**
- Open phpMyAdmin
- Select your database
- Import the file: `database/schema.sql`

#### 5. Access Your Application
- **For Fontys ICT server**:
  - Homepage: `https://i888908.apollo.fontysict.net/fed-workshops/`
  - Login: `https://i888908.apollo.fontysict.net/fed-workshops/login`
- **For subdomain**: 
  - Homepage: `http://fed-workshops.yourdomain.dev/`
  - Login: `http://fed-workshops.yourdomain.dev/login`
- **For subdirectory**:
  - Homepage: `http://yourdomain.com/workshopplanner/`
  - Login: `http://yourdomain.com/workshopplanner/login`

**Default Accounts:**
- Admin: `admin@fed.nl` / `admin123`
- Teacher: `teacher@fed.nl` / `admin123`

### ⚠️ Important Security Steps

1. **Change Default Passwords** immediately after setup
2. **Set Strong JWT Secret** in `.env` file
3. **Delete Setup File** `database/web-migrate.php` after setup
4. **Test All Functionality** before going live

### 🔄 Migrating Existing Data

If you have data from the Nuxt 3 version:

1. Add PostgreSQL credentials to `.env`:
   ```env
   POSTGRES_HOST=your_render_host
   POSTGRES_DB=workshop_planner
   POSTGRES_USER=your_postgres_user
   POSTGRES_PASSWORD=your_postgres_password
   ```

2. Use the web migration tool at: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php` (or appropriate URL for your setup)

### 🆘 Troubleshooting

**Database Connection Failed:**
- Check credentials in `.env`
- Ensure database and user exist
- Verify user has all privileges

**404 Errors:**
- Ensure `.htaccess` file is uploaded
- Check if mod_rewrite is enabled on your hosting

**Permission Errors:**
- Set file permissions: 644 for files, 755 for folders
- Ensure `.env` is readable by web server

**CSRF Token Errors:**
- Check if sessions are working
- Ensure temp directory is writable

### 📞 Need Help?

Check the full `README.md` for detailed instructions, security features, and troubleshooting guide.

---

**🎉 You're ready to manage workshops with enhanced security!**