# 🔧 DirectAdmin Setup Guide

## Quick Setup for i888908_workshopplanner Database

### 📋 What You Need
- Access to DirectAdmin panel
- Database: `i888908_workshopplanner` (already created)
- Database user credentials from DirectAdmin

### 🚀 Step-by-Step Setup

#### 1. Configure Environment File
- Rename `.env.fontys` to `.env`
- Edit `.env` with your actual DirectAdmin database credentials:
  ```env
  DB_HOST=localhost
  DB_NAME=i888908_workshopplanner
  DB_USER=i888908_your_actual_username
  DB_PASSWORD=your_actual_password
  JWT_SECRET=create-a-random-32-character-string-here
  ```

#### 2. Import Database via DirectAdmin

**Steps:**
1. Login to DirectAdmin
2. Go to "MySQL Management" 
3. Select database: `i888908_workshopplanner`
4. Click "Import Database" or "phpMyAdmin"
5. Upload the file: `database/directadmin-import.sql`
6. Click "Import" or "Go"

**Alternative if file upload fails:**
1. Open `database/directadmin-import.sql` in a text editor
2. Copy all the SQL content
3. In DirectAdmin MySQL Management, click "Query"
4. Paste the SQL content
5. Click "Execute"

#### 3. Verify Setup

Go to: `https://i888908.apollo.fontysict.net/fed-workshops/database/diagnose.php`

This should show:
- ✅ Database connection successful
- ✅ All tables exist: users, workshops, login_attempts, csrf_tokens
- ✅ Users table populated (2 users)

#### 4. Test Your Application

**URLs to test:**
- **Homepage**: `https://i888908.apollo.fontysict.net/fed-workshops/`
- **Login**: `https://i888908.apollo.fontysict.net/fed-workshops/login`

**Default accounts:**
- **Admin**: `admin@fed.nl` / `admin123`
- **Teacher**: `teacher@fed.nl` / `admin123`

### 🔐 Security Steps

1. **Login and change passwords** immediately
2. **Set a strong JWT_SECRET** in your `.env` file
3. **Delete setup files** after successful setup:
   - `database/directadmin-import.sql`
   - `database/diagnose.php`
   - `database/quick-setup.php`
   - `test-paths.php`

### 📊 Database Structure Created

The SQL file creates:

**Tables:**
- `users` - Admin and teacher accounts
- `workshops` - Workshop storage
- `login_attempts` - Security tracking
- `csrf_tokens` - CSRF protection

**Default Data:**
- Admin user: admin@fed.nl
- Teacher user: teacher@fed.nl
- Both with password: admin123

**Security Features:**
- Foreign key constraints
- Indexes for performance
- Password hashing
- CSRF token storage

### 🆘 Troubleshooting

**Import fails:**
- Check that database `i888908_workshopplanner` exists
- Verify your database user has CREATE/INSERT privileges
- Try copying SQL content and pasting in DirectAdmin Query tool

**Connection errors:**
- Double-check database credentials in `.env`
- Ensure database user has access to the specific database
- Use exact username provided by DirectAdmin

**Application doesn't load:**
- Check `.htaccess` file is uploaded
- Verify file permissions (644 for files, 755 for directories)
- Check error logs in DirectAdmin

### ✅ Success Checklist

- [ ] Database imported successfully via DirectAdmin
- [ ] `.env` file configured with correct credentials
- [ ] Diagnostic tool shows all green checkmarks
- [ ] Homepage loads at `https://i888908.apollo.fontysict.net/fed-workshops/`
- [ ] Can login with default accounts
- [ ] Default passwords changed
- [ ] Setup files deleted

---

**🎉 Your Workshop Planner should now be fully functional!**

### 📞 Support

If you encounter issues:
1. Check the diagnostic tool first
2. Verify database credentials in DirectAdmin
3. Check that all files were uploaded correctly
4. Ensure database user has proper privileges