# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a full-featured workshop management system built with Nuxt 3, using the Options API as requested. The application allows teachers to create and manage workshops while providing a public view for browsing the workshop schedule.

### Key Features
- **Teacher Authentication**: Simple JWT-based auth for teachers to manage workshops
- **Workshop Management**: Full CRUD operations for workshops with subject categorization
- **Password Management**: Change password functionality for all logged-in users
- **Public Schedule**: Anyone can view the workshop planning without authentication
- **Dual View System**: Toggle between workshop tiles and calendar view with advanced filtering
- **Subject Categories**: Dev, UX, Professional Skills (PO), Research, Portfolio, Misc (predefined by admin)
- **Day-based Planning**: Workshops are planned by day only (no specific hours)
- **Dark/Light Mode**: Toggle with easy-on-eyes pastel color scheme
- **WCAG AA Accessibility**: Compliant color contrast and keyboard navigation
- **Loading States**: Professional loading animations and user feedback
- **PWA Ready**: Favicon and manifest support for progressive web app capabilities
- **Responsive Design**: Works on all devices

## Development Commands

- `npm install` - Install dependencies
- `npm run dev` - Start development server (http://localhost:3000)
- `npm run build` - Build for production
- `npm run generate` - Generate static site
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint
- `npm run lint:fix` - Fix ESLint issues
- `npm run start` - Start production server

## Database Commands

- `npm run db:push` - Push schema to database
- `npm run db:seed` - Seed with demo data
- `npm run db:studio` - Open Prisma Studio

## Architecture

### Tech Stack
- **Framework**: Nuxt 3 with TypeScript
- **State Management**: Pinia stores
- **Styling**: Custom CSS with CSS variables for theming
- **Authentication**: JWT tokens with bcryptjs for password hashing
- **Database**: PostgreSQL with Prisma ORM
- **Hosting**: Render (PostgreSQL + Node.js hosting)
- **PWA**: Manifest and favicon support for app-like experience

### Project Structure
```
├── assets/css/           # Global styles and theming
├── components/           # Vue components (currently using layouts/pages)
├── layouts/             # Nuxt layouts (default.vue)
├── middleware/          # Route middleware (auth.ts)
├── pages/               # Route pages (index.vue, login.vue, dashboard.vue, change-password.vue)
├── server/api/          # API endpoints
│   ├── auth/           # Authentication endpoints
│   └── workshops/      # Workshop CRUD endpoints
├── store/              # Pinia stores (auth.ts, workshops.ts)
├── types/              # TypeScript definitions
└── utils/              # Utility functions
```

### Color System & Accessibility
The app uses CSS custom properties for consistent theming:
- **WCAG AA Compliance**: All colors meet 4.5:1+ contrast ratio requirements
- **Subjects**: Each subject has unique pastel colors (Dev=blue, UX=purple, PO=green, etc.)
- **Themes**: Light and dark mode with automatic system preference detection
- **Loading States**: Professional spinner animations with proper contrast
- **Responsive**: Mobile-first approach with flexible layouts

### Authentication
- **Test Accounts**: Basic admin/teacher accounts seeded for development (no demo workshops)
- **JWT Tokens**: 7-day expiry, stored in HTTP-only cookies
- **Role-based Access**: Teachers can create/edit/delete their own workshops, admins can manage all

### API Endpoints
- `POST /api/auth/login` - Login with email/password
- `POST /api/auth/register` - Register new teacher account
- `POST /api/auth/change-password` - Change user password (auth required)
- `GET /api/auth/me` - Get current user info
- `GET /api/workshops` - List all workshops (public)
- `POST /api/workshops` - Create workshop (auth required)
- `PUT /api/workshops/[id]` - Update workshop (auth required)
- `DELETE /api/workshops/[id]` - Delete workshop (auth required)

### Database Setup

The app uses PostgreSQL with Prisma ORM connected to Coolify on Hetzner VPS:

```bash
# Database commands
npm run db:push        # Push schema to database
npm run db:seed        # Seed with demo data
npm run db:studio      # Open Prisma Studio
```

**Database Schema:**
- **Users**: Teacher accounts with JWT authentication
- **Workshops**: Subject-categorized workshops with date-based planning
- **Relationships**: One-to-many (User → Workshops)

**Seeding Policy:**
- Only basic admin/teacher test accounts are seeded
- No demo workshop data is created in production
- Clean deployment with empty workshop database

**Environment Variables:**
```env
DATABASE_URL="postgresql://username:password@postgresql:5432/database"
DIRECT_URL="postgresql://username:password@postgresql:5432/database"
JWT_SECRET="your-secret-key"
```

**Note**: For Coolify deployment, both URLs typically use the same internal connection string since app and database are on the same infrastructure.

## Database Management

**IMPORTANT**: Database changes (adding users, modifying data) require manual SQL execution. When users need to be added or database changes are required:

1. **Generate SQL**: Claude Code will provide the exact SQL statements needed
2. **Manual Execution**: The user must run these SQL statements directly in their database (via Render dashboard, pgAdmin, or database client)
3. **No Automatic Schema Changes**: Schema migrations use Prisma, but data changes require manual SQL execution

**Example Workflow for Coolify:**
- User requests new accounts to be added
- Claude generates SQL INSERT statements with bcrypt-hashed passwords
- User executes SQL via app container: `node_modules/.bin/prisma db execute --schema prisma/schema.prisma --stdin`
- Paste SQL and press Ctrl+D to execute
- Changes take effect immediately on the live application

**Generating Password Hashes:**
```bash
# In the app container terminal
node -e "const bcrypt = require('bcryptjs'); console.log(bcrypt.hashSync('password123', 12));"
```

## Development Notes

- **Options API**: The project uses Vue's Options API as requested
- **Database**: Uses PostgreSQL with Prisma ORM and automatic type generation
- **WCAG AA Colors**: All colors meet accessibility standards with 4.5:1+ contrast ratios
- **Dual Views**: Implemented tile and calendar views with persistent user preference
- **Loading States**: Added professional loading animations for better UX
- **PWA Features**: Favicon and manifest support for app-like experience
- **Teacher Focus**: UI optimized for teacher workflow while keeping public view clean
- **Day Planning**: Workshops are date-based only, giving teachers flexibility during their day

## Deployment

The app is deployed on Coolify with Hetzner VPS and PostgreSQL database. It's also compatible with Render, Vercel, Netlify, or any Node.js hosting platform.

### Coolify Deployment (Current)
- **Infrastructure**: Hetzner VPS with Coolify self-hosted PaaS
- **Database**: PostgreSQL container managed by Coolify
- **Server**: Node.js hosting with automatic deploys from GitHub
- **Environment**: Production-ready with proper SSL and internal networking

### Coolify Setup Steps
1. Create PostgreSQL database service in Coolify
2. Create Node.js application and connect to Git repository
3. Set environment variables in Coolify dashboard:
   ```env
   DATABASE_URL=postgresql://username:password@postgresql:5432/database
   DIRECT_URL=postgresql://username:password@postgresql:5432/database
   JWT_SECRET=your-secret-key
   ```
4. Deploy application - schema will be created automatically via postinstall hook
5. Add production users via app container terminal

### Build Process
- Automatic database schema creation via postinstall hook
- Production build optimization
- Static asset generation and caching

## Troubleshooting

**Database Connection Issues:**
1. Check if Coolify PostgreSQL service is running
2. Verify connection string format matches internal Coolify networking
3. Ensure both `DATABASE_URL` and `DIRECT_URL` use the same internal connection
4. Try `npm run db:push` to test connection from app container
5. Check Coolify logs for deployment issues
6. Verify environment variables are set correctly in Coolify dashboard

**Common Issues:**
- **Missing start script**: Ensure `package.json` has `"start": "node .output/server/index.mjs"`
- **Schema not created**: Database schema is automatically created via postinstall hook
- **Environment variables**: Double-check all required env vars are set in Coolify dashboard
- **User login issues**: Verify password hashes are generated correctly using bcryptjs in app container

**View System:**
- Toggle between tile and calendar views using the view switcher
- Filters work in both views and persist across view changes
- Calendar navigation allows browsing different months

**Authentication:**
- Basic test accounts seeded for development (no demo workshops)
- JWT tokens expire after 7 days
- Logout properly redirects to homepage with page refresh
- Change password functionality available at `/change-password` (requires login)

**Password Management:**
- Users can change their password via the dashboard "Change Password" button
- Requires current password verification for security
- New passwords must be at least 6 characters long
- Success message shown and redirects to dashboard after password change