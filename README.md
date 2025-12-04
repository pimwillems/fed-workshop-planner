# Workshop Planner 📚

A modern, accessible workshop management system built with Nuxt 3, allowing teachers to schedule and manage educational workshops with a clean public interface for attendees.

## ✨ Features

### 🔐 **Teacher Authentication**
- Secure JWT-based authentication system
- Role-based access control (Teacher/Admin)
- Demo accounts ready for testing

### 📋 **Workshop Management** 
- Full CRUD operations for workshops
- Subject categorization (Development, UX, Professional Skills, Research, Portfolio, Misc)
- Day-based scheduling for flexible planning
- Rich text descriptions and metadata

### 📅 **Dual View Modes**
- **Workshop Tiles**: Card-based layout with detailed information and subject color coding
- **Calendar View**: Monthly calendar with interactive workshop blocks and navigation
- Easy toggle between viewing preferences with persistent state
- Advanced filtering by subject and date in both views

### 🎨 **Modern UI/UX**
- **WCAG AA compliant** color scheme for accessibility
- Dark/light mode with system preference detection
- Responsive design for all devices
- Smooth animations and loading states
- Professional favicon and PWA manifest support

### 🔍 **Advanced Filtering**
- Filter by subject category
- Filter by specific date
- Real-time search and filtering
- Clear and intuitive controls

## 🚀 Quick Start

### Prerequisites
- Node.js 18+
- PostgreSQL database (Docker, local installation, or cloud service)
- Docker (recommended for local development)

### Local Development Setup

#### Option A: Using Docker PostgreSQL (Recommended)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd workshop-planner
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Start PostgreSQL with Docker**
   ```bash
   docker run --name postgres17 -e POSTGRES_PASSWORD=password -p 5432:5432 -d postgres:17-alpine
   ```

4. **Set up environment variables**

   Create a `.env.local` file (or `.env`) in the project root:
   ```env
   DATABASE_URL="postgresql://postgres:password@localhost:5432/demo_workshops"
   DIRECT_URL="postgresql://postgres:password@localhost:5432/demo_workshops"
   JWT_SECRET="local-dev-secret-key-do-not-use-in-production"
   ```

5. **Set up the database schema**
   ```bash
   npm run db:push
   ```

6. **Seed the database with test data**

   For a quick start with admin user and 3 sample workshops:
   ```bash
   node scripts/seed-local.mjs
   ```

   This creates:
   - **Admin user** - Email: `admin`, Password: `admin`
   - **3 workshops** in December 2025 (DEV, UX, and PO subjects)

7. **Start development server**
   ```bash
   npm run dev
   ```

8. **Open your browser**

   Navigate to [http://localhost:3000](http://localhost:3000)

   Login at [http://localhost:3000/login](http://localhost:3000/login) with:
   - **Email:** `admin`
   - **Password:** `admin`

#### Option B: Using Existing PostgreSQL Installation

1. **Clone and install** (same as Option A steps 1-2)

2. **Create database**
   ```bash
   createdb demo_workshops
   ```

3. **Set up environment variables**

   Create a `.env.local` file with your PostgreSQL credentials:
   ```env
   DATABASE_URL="postgresql://your_user:your_password@localhost:5432/demo_workshops"
   DIRECT_URL="postgresql://your_user:your_password@localhost:5432/demo_workshops"
   JWT_SECRET="local-dev-secret-key-do-not-use-in-production"
   ```

4. **Continue with steps 5-8 from Option A**

### Production Setup

For production deployment (Coolify, Render, Vercel, etc.), see the [Deployment](#-deployment) section below.

## 🏗️ Technology Stack

### Frontend
- **Nuxt 3** - Vue.js framework with SSR/SSG
- **TypeScript** - Type-safe development
- **Pinia** - State management
- **CSS Variables** - Custom theming system

### Backend  
- **Nitro** - Server engine (built into Nuxt 3)
- **Prisma ORM** - Database management
- **JWT** - Authentication tokens
- **bcryptjs** - Password hashing

### Database
- **PostgreSQL** - Primary database
- **Coolify PostgreSQL** - Self-hosted PostgreSQL on Hetzner VPS

## 📁 Project Structure

```
workshop-planner/
├── assets/css/          # Global styles and theming
├── layouts/             # Nuxt layouts
├── middleware/          # Route middleware (auth)
├── pages/               # Application pages
│   ├── index.vue        # Public workshop schedule
│   ├── login.vue        # Authentication
│   └── dashboard.vue    # Teacher dashboard
├── server/api/          # API endpoints
│   ├── auth/           # Authentication routes
│   └── workshops/      # Workshop CRUD routes
├── store/              # Pinia stores
├── types/              # TypeScript definitions
├── utils/              # Utility functions
└── prisma/             # Database schema and seeds
```

## 🎯 Usage

### For Teachers
1. **Login** with your credentials
2. **Create workshops** with title, description, subject, and date
3. **Manage existing workshops** - edit or delete as needed
4. **View your workshops** in tiles or calendar format

### For Students/Public
1. **Browse workshops** on the homepage
2. **Switch between views** - tiles or calendar
3. **Filter workshops** by subject or date
4. **View workshop details** including teacher and timing

## 🛠️ Development

### Available Scripts

```bash
npm run dev          # Start development server
npm run build        # Build for production
npm run generate     # Generate static site
npm run preview      # Preview production build
npm run start        # Start production server
npm run lint         # Run ESLint
npm run lint:fix     # Fix ESLint issues

# Database commands
npm run db:push      # Push schema to database  
npm run db:seed      # Seed with demo data
npm run db:studio    # Open Prisma Studio
```

### Environment Variables

| Variable | Description | Required |
|----------|-------------|----------|
| `DATABASE_URL` | PostgreSQL connection string (internal networking) | Yes |
| `DIRECT_URL` | PostgreSQL direct connection for migrations (same as DATABASE_URL for Coolify) | Yes |
| `JWT_SECRET` | Secret key for JWT token signing | Yes |

**Note**: For Coolify deployment, both URLs typically use the same internal connection string since app and database are on the same infrastructure.

## ♿ Accessibility

This application follows **WCAG AA guidelines**:

- **Color Contrast**: 4.5:1+ ratio for all text
- **Keyboard Navigation**: Full keyboard accessibility
- **Screen Readers**: Semantic HTML and ARIA labels
- **Focus Indicators**: Clear visual focus states
- **Reduced Motion**: Respects user motion preferences

## 🚢 Deployment

### Coolify on Hetzner VPS (Recommended for full-stack)
1. Set up Coolify on your Hetzner VPS
2. Create a PostgreSQL database service in Coolify
3. Create a Node.js application and connect to your Git repository
4. Set environment variables in Coolify dashboard:
   ```env
   DATABASE_URL=postgresql://username:password@postgresql:5432/database
   DIRECT_URL=postgresql://username:password@postgresql:5432/database
   JWT_SECRET=<your-secret>
   ```
5. Deploy automatically on git push
6. Database schema will be created automatically via postinstall hook
7. Add production users via app container terminal

### Alternative Deployments

#### Render
1. Connect your repository to Render
2. Create a PostgreSQL database on Render
3. Set environment variables with internal/external URLs
4. Deploy automatically on git push

#### Vercel (Static/Serverless)
1. Connect your repository to Vercel
2. Set environment variables in Vercel dashboard
3. Deploy automatically on git push

#### Self-Hosted VPS
1. Build the application: `npm run build`
2. Start with: `npm run start` (production server)
3. Use PM2 or similar for production process management

## 🔒 Security Features

- **JWT Authentication** with 7-day expiry
- **Password Hashing** with bcryptjs
- **HTTP-only Cookies** for token storage
- **Input Validation** on all forms
- **SQL Injection Protection** via Prisma ORM
- **XSS Protection** through Vue's built-in sanitization

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- Check the [troubleshooting guide](CLAUDE.md)
- Review existing [issues](https://github.com/your-repo/workshop-planner/issues)
- Create a new issue for bugs or feature requests

---

Built with ❤️ using Nuxt 3 and modern web technologies.