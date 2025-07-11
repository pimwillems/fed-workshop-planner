# 📋 Fontys ICT Server - URL Reference

## Your Workshop Planner URLs

### 🏠 Main Application
- **Homepage**: `https://i888908.apollo.fontysict.net/fed-workshops/`
- **Login**: `https://i888908.apollo.fontysict.net/fed-workshops/login`
- **Register**: `https://i888908.apollo.fontysict.net/fed-workshops/register`
- **Dashboard**: `https://i888908.apollo.fontysict.net/fed-workshops/dashboard`
- **Change Password**: `https://i888908.apollo.fontysict.net/fed-workshops/change-password`

### 🛠️ Setup & Testing
- **Database Setup Wizard**: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php`
- **Database Diagnostic Tool**: `https://i888908.apollo.fontysict.net/fed-workshops/database/diagnose.php`
- **Path Configuration Test**: `https://i888908.apollo.fontysict.net/fed-workshops/test-paths.php`

### 🔌 API Endpoints
- **Get Workshops**: `https://i888908.apollo.fontysict.net/fed-workshops/api/workshops`
- **Create Workshop**: `https://i888908.apollo.fontysict.net/fed-workshops/api/workshops` (POST)
- **Update Workshop**: `https://i888908.apollo.fontysict.net/fed-workshops/api/workshops/{id}` (PUT)
- **Delete Workshop**: `https://i888908.apollo.fontysict.net/fed-workshops/api/workshops/{id}` (DELETE)
- **Get User Info**: `https://i888908.apollo.fontysict.net/fed-workshops/api/auth/me`

### 🎨 Static Assets
- **CSS**: `https://i888908.apollo.fontysict.net/fed-workshops/assets/css/style.css`
- **JavaScript**: `https://i888908.apollo.fontysict.net/fed-workshops/assets/js/app.js`

### 🔐 Default Login Accounts
- **Admin**: `admin@fed.nl` / `admin123`
- **Teacher**: `teacher@fed.nl` / `admin123`

**⚠️ SECURITY**: Change these default passwords immediately after first login!

### ✅ Quick Setup Checklist
1. [ ] Upload files to `public_html/fed-workshops/`
2. [ ] Configure `.env` with database credentials
3. [ ] Create MySQL database on Fontys server
4. [ ] Run setup wizard: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php`
5. [ ] Test configuration: `https://i888908.apollo.fontysict.net/fed-workshops/test-paths.php`
6. [ ] Access homepage: `https://i888908.apollo.fontysict.net/fed-workshops/`
7. [ ] Login and change default passwords
8. [ ] Delete test files for security

### 🔧 Expected Configuration
- **Base Path**: `/fed-workshops/`
- **Installation Type**: Subdirectory
- **HTTPS**: Automatically enabled
- **URL Structure**: All URLs include `/fed-workshops/` prefix

---

**📞 Need Help?** Check `FONTYS-SETUP.md` for detailed instructions or `test-paths.php` for configuration verification.