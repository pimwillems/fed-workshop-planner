# Workshop Planner - PHP Version

A complete conversion of the original Nuxt 3 workshop management system to PHP with MySQL, featuring comprehensive security protections and all original functionality.

## 🚀 Features

### Core Functionality
- **Public Workshop Schedule**: Browse workshops with tile and calendar views
- **Teacher Authentication**: JWT-based secure login/registration
- **Workshop Management**: Full CRUD operations for teachers
- **Password Management**: Secure password change functionality
- **Subject Categories**: Dev, UX, Professional Skills, Research, Portfolio, Miscellaneous
- **Date-based Planning**: Workshops planned by day for teacher flexibility
- **Advanced Filtering**: Filter by subject, date, and search text
- **Responsive Design**: Works seamlessly on all devices

### Security Features
- **CSRF Protection**: All forms protected against cross-site request forgery
- **XSS Prevention**: Input sanitization and output escaping
- **SQL Injection Protection**: Prepared statements and parameterized queries
- **Rate Limiting**: Login attempt throttling with IP tracking
- **Secure Headers**: X-Frame-Options, X-XSS-Protection, CSP, etc.
- **Password Security**: Bcrypt hashing with salt
- **JWT Security**: Signed tokens with expiration
- **Session Security**: Secure session configuration
- **Input Validation**: Server-side validation for all user inputs

### Accessibility & UX
- **WCAG AA Compliance**: 4.5:1+ contrast ratios
- **Dark/Light Mode**: Automatic system preference detection
- **Keyboard Navigation**: Full keyboard accessibility
- **Screen Reader Support**: Proper ARIA labels and semantic HTML
- **Loading States**: Professional loading animations
- **Error Handling**: Comprehensive error messages and recovery

## 📁 Project Structure

```
php-version/
├── index.php                 # Main entry point
├── .htaccess                 # Apache configuration & security
├── .env.example              # Environment configuration template
├── config/
│   ├── app.php              # Application configuration
│   └── database.php         # Database connection
├── src/
│   ├── Core/                # Core framework classes
│   │   ├── Router.php       # HTTP routing
│   │   ├── Request.php      # Request handling
│   │   ├── Response.php     # Response formatting
│   │   ├── Security.php     # Security utilities
│   │   └── JWT.php          # JWT token management
│   └── Controllers/         # MVC controllers
│       ├── AuthController.php
│       ├── WorkshopController.php
│       ├── HomeController.php
│       └── DashboardController.php
├── views/                   # PHP templates
│   ├── layout.php           # Main layout template
│   ├── index.php            # Public homepage
│   ├── dashboard.php        # Teacher dashboard
│   └── auth/                # Authentication pages
├── assets/                  # Static assets
│   ├── css/style.css        # WCAG AA compliant styles
│   └── js/app.js            # Client-side JavaScript
└── database/
    ├── schema.sql           # MySQL database schema
    └── migrate.php          # Data migration script
```

## 🛠 Installation (No Command Line Required)

### Prerequisites
- PHP 7.4+ (PHP 8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Web hosting with cPanel/File Manager access
- FTP/SFTP client (FileZilla, WinSCP, etc.) OR web-based file manager

### Step-by-Step Setup

#### Method 1: Using FTP/File Manager (Recommended)

1. **Upload PHP Files**
   - Download the `php-version` folder to your computer
   - Using FTP client or cPanel File Manager, upload all files to your web directory
   - Example paths:
     - **Fontys ICT Server**: Upload to `public_html/fed-workshops/`
     - **Subdomain** (fed-workshops.domain.com): Upload to the subdomain's root directory
     - **Subdirectory**: Upload to `public_html/workshopplanner/`
     - **Other hosts**: Upload to appropriate web root directory

2. **Configure Environment**
   - Rename `.env.example` to `.env` using file manager
   - Edit `.env` file using cPanel File Editor or download/edit/upload:
   ```env
   # Required settings
   DB_HOST=localhost
   DB_NAME=your_database_name
   DB_USER=your_database_username
   DB_PASSWORD=your_database_password
   JWT_SECRET=generate-a-random-32-character-string-here
   DEBUG=false
   ```

3. **Create MySQL Database**
   
   **Using cPanel/phpMyAdmin:**
   - Go to cPanel → MySQL Databases (or Databases)
   - Create new database: `workshop_planner` (or any name you prefer)
   - Create new user with all privileges on this database
   - Note down the database name, username, and password for `.env`

   **Using phpMyAdmin directly:**
   - Login to phpMyAdmin
   - Click "New" to create database
   - Name it `workshop_planner`
   - Set collation to `utf8mb4_unicode_ci`

4. **Import Database Schema**
   
   **Option A - Using phpMyAdmin (Recommended):**
   - Open phpMyAdmin
   - Select your database
   - Click "Import" tab
   - Upload `database/schema.sql` file
   - Click "Go" to execute

   **Option B - Using cPanel MySQL:**
   - Go to cPanel → phpMyAdmin
   - Select your database
   - Copy contents of `database/schema.sql`
   - Go to "SQL" tab
   - Paste the SQL and click "Go"

#### Method 2: Using Web-Based Migration Tool

1. **Upload Files** (same as Method 1, step 1)

2. **Configure Environment** (same as Method 1, step 2)

3. **Create Database** (same as Method 1, step 3)

4. **Run Web Migration**
   - **Fontys ICT Server**: Open your browser to: `https://i888908.apollo.fontysict.net/fed-workshops/database/web-migrate.php`
   - **Subdomain**: Open your browser to: `http://fed-workshops.yourdomain.dev/database/web-migrate.php`
   - **Subdirectory**: Open your browser to: `http://yourdomain.com/workshopplanner/database/web-migrate.php`
   - The script will automatically:
     - Create all database tables
     - Set up indexes and relationships
     - Create default admin accounts
     - Optionally generate sample data
   - Follow the on-screen instructions

#### Method 3: Manual Database Setup

If you can't run PHP scripts, create the database manually:

1. **Upload Files and Configure** (same as Methods 1-2)

2. **Execute SQL Manually**
   - Open `database/schema.sql` in a text editor
   - Copy all SQL statements
   - In phpMyAdmin, go to SQL tab
   - Paste and execute each section:
     - First: CREATE TABLE statements
     - Second: INSERT statements for default users
     - Third: CREATE INDEX statements

### Web Server Configuration

**Apache (Most Common)**: The included `.htaccess` file handles all configuration automatically. No changes needed.

**Nginx**: Contact your hosting provider to add this configuration, or if you have access:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}

# Deny access to sensitive files
location ~ /\.(env|htaccess)$ { deny all; }
location ~ /(config|src|database|views)/ { deny all; }
```

### File Permissions

Most shared hosting providers set correct permissions automatically. If needed:

**Using cPanel File Manager:**
- Select files/folders
- Right-click → Change Permissions
- Set folders to `755` and files to `644`
- Set `.env` to `644` (read-only)

## 🗄 Database Migration from Existing Data

### From Existing PostgreSQL Data (Nuxt 3 Version)

If you have existing data in the Nuxt 3 version, you can transfer it using these methods:

#### Method 1: Web-Based Migration (Easiest)

1. **Configure PostgreSQL Connection** in `.env`:
   ```env
   # Add these lines to your .env file
   POSTGRES_HOST=your_render_postgres_host
   POSTGRES_PORT=5432
   POSTGRES_DB=workshop_planner
   POSTGRES_USER=your_postgres_user
   POSTGRES_PASSWORD=your_postgres_password
   ```

2. **Run Web Migration**:
   - Go to: `http://yourdomain.com/database/migrate.php`
   - The script will automatically detect your PostgreSQL connection
   - It will migrate all users and workshops to MySQL
   - Follow the prompts to complete migration

#### Method 2: Manual Data Export/Import

If you can't connect directly to PostgreSQL:

1. **Export from PostgreSQL** (using pgAdmin or similar):
   ```sql
   -- Export users
   COPY (SELECT id, email, name, password, role, created_at, updated_at FROM users) 
   TO '/tmp/users.csv' WITH CSV HEADER;
   
   -- Export workshops
   COPY (SELECT id, title, description, subject, date, teacher_id, created_at, updated_at FROM workshops) 
   TO '/tmp/workshops.csv' WITH CSV HEADER;
   ```

2. **Import to MySQL** using phpMyAdmin:
   - Select your database
   - Go to "Import" tab
   - Upload the CSV files
   - Map columns correctly during import

#### Method 3: SQL Script Generation

1. **Generate INSERT Statements** from your PostgreSQL data
2. **Modify for MySQL syntax** (replace `'YYYY-MM-DD HH:MM:SS.mmm+TZ'` with `'YYYY-MM-DD HH:MM:SS'`)
3. **Execute in phpMyAdmin** SQL tab

### Manual SQL Execution for New Installations

For fresh installations without existing data, just run the schema.sql file as described in the installation section.

## 🔐 Security Features

### Implemented Protections

1. **CSRF Protection**
   - Unique tokens for each session
   - Token validation on all state-changing requests
   - Automatic token renewal

2. **XSS Prevention**
   - Input sanitization using `htmlspecialchars()`
   - Output escaping in templates
   - Content Security Policy headers

3. **SQL Injection Protection**
   - All queries use prepared statements
   - Parameter binding for user inputs
   - No dynamic SQL construction

4. **Authentication Security**
   - Bcrypt password hashing (cost: 12)
   - JWT tokens with expiration
   - Rate limiting on login attempts
   - Secure session configuration

5. **HTTP Security Headers**
   - X-Frame-Options: DENY
   - X-Content-Type-Options: nosniff
   - X-XSS-Protection: 1; mode=block
   - Content-Security-Policy
   - Referrer-Policy: strict-origin-when-cross-origin

6. **Input Validation**
   - Server-side validation for all inputs
   - Type checking and length limits
   - Email format validation
   - Date format validation

## 🎨 Frontend Features

### Design & Accessibility
- **WCAG AA Compliant**: 4.5:1+ contrast ratios for all text
- **Dark/Light Mode**: Automatic system preference detection with manual toggle
- **Responsive Design**: Mobile-first approach with flexible layouts
- **Loading States**: Professional spinner animations
- **Error Handling**: User-friendly error messages with recovery options

### User Interface
- **Dual View System**: Toggle between workshop tiles and calendar view
- **Advanced Filtering**: Subject, date, and text search filters
- **Real-time Updates**: AJAX-powered interactions without page reloads
- **Keyboard Navigation**: Full keyboard accessibility support
- **Auto-save**: Optional form auto-save functionality

## 🔧 Configuration

### Environment Variables

```env
# Application
DEBUG=false
APP_ENV=production

# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=workshop_planner
DB_USER=your_username
DB_PASSWORD=your_password

# Security
JWT_SECRET=your-super-secret-jwt-key
CSRF_EXPIRATION=3600
MAX_LOGIN_ATTEMPTS=5
LOCKOUT_DURATION=900
```

### Default Admin Accounts

The system creates default accounts for testing:
- **Admin**: admin@fed.nl / admin123
- **Teacher**: teacher@fed.nl / admin123

**⚠️ Important**: Change these passwords immediately in production!

## 🧪 Testing

### Manual Testing Checklist

1. **Authentication**
   - [ ] User registration with validation
   - [ ] Login with rate limiting
   - [ ] Password change functionality
   - [ ] JWT token expiration handling

2. **Workshop Management**
   - [ ] Create workshop with all fields
   - [ ] Edit existing workshops
   - [ ] Delete workshops with confirmation
   - [ ] Filter and search functionality

3. **Security**
   - [ ] CSRF token validation
   - [ ] SQL injection attempts (should fail)
   - [ ] XSS attempts (should be escaped)
   - [ ] Unauthorized access attempts

4. **User Experience**
   - [ ] Dark/light mode toggle
   - [ ] Responsive design on mobile
   - [ ] Keyboard navigation
   - [ ] Loading states and error handling

## 🚀 Deployment

### Production Checklist

1. **Security**
   - [ ] Change default passwords
   - [ ] Set strong JWT secret
   - [ ] Enable HTTPS
   - [ ] Configure secure session cookies
   - [ ] Set up proper file permissions

2. **Performance**
   - [ ] Enable gzip compression
   - [ ] Set up asset caching
   - [ ] Optimize database indexes
   - [ ] Configure OPcache

3. **Monitoring**
   - [ ] Set up error logging
   - [ ] Monitor login attempts
   - [ ] Track performance metrics

### Hosting Requirements

- **Shared Hosting**: Most shared hosts support this configuration
- **VPS/Dedicated**: Full control over security and performance settings
- **Cloud Platforms**: Compatible with AWS, Google Cloud, Azure, etc.

## 📈 Performance

### Optimizations Included

- **Database Indexes**: Optimized queries for workshops and users
- **Asset Compression**: Gzip compression for CSS/JS
- **Caching Headers**: Browser caching for static assets
- **Optimized Queries**: Efficient JOIN operations
- **Minimal Dependencies**: Pure PHP with no heavy frameworks

## 🔍 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `.env`
   - Ensure MySQL service is running
   - Verify user permissions

2. **404 Errors on Routes**
   - Ensure `.htaccess` is working (Apache)
   - Check nginx configuration
   - Verify mod_rewrite is enabled

3. **Permission Denied Errors**
   - Set proper file permissions (755 for directories, 644 for files)
   - Ensure web server can read all files

4. **CSRF Token Errors**
   - Check if sessions are working
   - Ensure session directory is writable
   - Verify cookie settings

## 🔄 Migration Summary

### What Was Converted

✅ **Completed Conversions**:
- Nuxt 3 → PHP MVC architecture
- PostgreSQL → MySQL database schema
- Vue.js components → PHP templates
- TypeScript → PHP with strong typing
- Pinia stores → PHP session management
- Prisma ORM → Pure PHP with PDO
- JWT authentication system
- All CRUD operations
- Advanced filtering and search
- Responsive design and dark mode
- WCAG AA accessibility compliance

✅ **Enhanced Security**:
- CSRF protection (not in original)
- Rate limiting (not in original)
- Comprehensive input validation
- SQL injection protection
- XSS prevention
- Secure HTTP headers

### Data Preservation

All data from the original system can be migrated:
- User accounts (with password hashes)
- Workshop records
- Subject categorizations
- Relationships between users and workshops

## 📞 Support

For issues or questions about this PHP conversion:

1. Check the troubleshooting section above
2. Review the original Nuxt 3 version documentation
3. Examine the code comments for implementation details

The PHP version maintains 100% feature parity with the original Nuxt 3 version while adding comprehensive security protections against common web vulnerabilities.

---

**🎉 Your workshop planner is now ready for production use with enterprise-grade security!**