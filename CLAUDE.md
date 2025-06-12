# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a full-featured workshop management system built with Nuxt 3, using the Options API as requested. The application allows teachers to create and manage workshops while providing a public view for browsing the workshop schedule.

### Key Features
- **Teacher Authentication**: Simple JWT-based auth for teachers to manage workshops
- **Workshop Management**: Full CRUD operations for workshops with subject categorization
- **Public Schedule**: Anyone can view the workshop planning without authentication
- **Subject Categories**: Dev, UX, Professional Skills (PO), Research, Portfolio, Misc (predefined by admin)
- **Day-based Planning**: Workshops are planned by day only (no specific hours)
- **Dark/Light Mode**: Toggle with easy-on-eyes pastel color scheme
- **Responsive Design**: Works on all devices

## Development Commands

- `npm install` - Install dependencies
- `npm run dev` - Start development server (http://localhost:3000)
- `npm run build` - Build for production
- `npm run generate` - Generate static site
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint
- `npm run lint:fix` - Fix ESLint issues

## Architecture

### Tech Stack
- **Framework**: Nuxt 3 with TypeScript
- **State Management**: Pinia stores
- **Styling**: Custom CSS with CSS variables for theming
- **Authentication**: JWT tokens with bcryptjs for password hashing
- **Database**: PostgreSQL with Prisma ORM
- **Hosting**: Supabase (PostgreSQL)

### Project Structure
```
├── assets/css/           # Global styles and theming
├── components/           # Vue components (currently using layouts/pages)
├── layouts/             # Nuxt layouts (default.vue)
├── middleware/          # Route middleware (auth.ts)
├── pages/               # Route pages (index.vue, login.vue, dashboard.vue)
├── server/api/          # API endpoints
│   ├── auth/           # Authentication endpoints
│   └── workshops/      # Workshop CRUD endpoints
├── store/              # Pinia stores (auth.ts, workshops.ts)
├── types/              # TypeScript definitions
└── utils/              # Utility functions
```

### Color System
The app uses CSS custom properties for consistent theming:
- **Subjects**: Each subject has unique pastel colors (Dev=blue, UX=purple, PO=green, etc.)
- **Themes**: Light and dark mode with automatic system preference detection
- **Responsive**: Mobile-first approach with flexible layouts

### Authentication
- **Demo Accounts**: 
  - Admin: admin@workshop.com / admin123
  - Teacher: teacher@workshop.com / admin123
- **JWT Tokens**: 7-day expiry, stored in HTTP-only cookies
- **Role-based Access**: Teachers can create/edit/delete their own workshops, admins can manage all

### API Endpoints
- `POST /api/auth/login` - Login with email/password
- `POST /api/auth/register` - Register new teacher account
- `GET /api/auth/me` - Get current user info
- `GET /api/workshops` - List all workshops (public)
- `POST /api/workshops` - Create workshop (auth required)
- `PUT /api/workshops/[id]` - Update workshop (auth required)
- `DELETE /api/workshops/[id]` - Delete workshop (auth required)

### Database Setup

The app uses PostgreSQL with Prisma ORM connected to Supabase:

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

**Environment Variables:**
```env
DATABASE_URL="postgresql://postgres:password@db.supabase.co:5432/postgres?sslmode=require"
JWT_SECRET="your-secret-key"
```

## Development Notes

- **Options API**: The project uses Vue's Options API as requested
- **Database**: Uses PostgreSQL with Prisma ORM and automatic type generation
- **Pastel Colors**: Easy-on-eyes color palette with proper contrast ratios
- **Teacher Focus**: UI optimized for teacher workflow while keeping public view clean
- **Day Planning**: Workshops are date-based only, giving teachers flexibility during their day

## Deployment

The app is ready for deployment on Vercel, Netlify, or any Node.js hosting platform. The database is already configured with Supabase for production use.

## Troubleshooting

**Database Connection Issues:**
1. Check if Supabase project is active (not paused)
2. Verify connection string format includes `?sslmode=require`
3. Ensure database server is accessible from your network
4. Try `npm run db:push` to test connection