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
- PostgreSQL database (Render recommended for deployment)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd workshop-planner
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Set up environment variables**
   ```bash
   cp .env.example .env
   ```
   
   Update `.env` with your database credentials:
   ```env
   DATABASE_URL="postgresql://username:password@host:port/database"
   DIRECT_URL="postgresql://username:password@host:port/database"
   JWT_SECRET="your-secure-secret-key"
   ```

4. **Set up the database**
   ```bash
   npm run db:push
   npm run db:seed
   ```

5. **Start development server**
   ```bash
   npm run dev
   ```

6. **Open your browser**
   Navigate to `http://localhost:3000`

## 🧪 Demo Accounts

The application comes with pre-seeded demo accounts:

- **Admin**: `admin@workshop.com` / `admin123`
- **Teacher**: `teacher@workshop.com` / `admin123`

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
- **Render PostgreSQL** - Cloud-hosted PostgreSQL database

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
| `DATABASE_URL` | PostgreSQL connection string (pooled/internal) | Yes |
| `DIRECT_URL` | PostgreSQL direct connection for migrations (external) | Yes |
| `JWT_SECRET` | Secret key for JWT token signing | Yes |

**Note**: For Render deployment, use the internal URL for `DATABASE_URL` and external URL for `DIRECT_URL`.

## ♿ Accessibility

This application follows **WCAG AA guidelines**:

- **Color Contrast**: 4.5:1+ ratio for all text
- **Keyboard Navigation**: Full keyboard accessibility
- **Screen Readers**: Semantic HTML and ARIA labels
- **Focus Indicators**: Clear visual focus states
- **Reduced Motion**: Respects user motion preferences

## 🚢 Deployment

### Render (Recommended for full-stack)
1. Connect your repository to Render
2. Create a PostgreSQL database on Render
3. Set environment variables:
   ```env
   DATABASE_URL=<render-postgres-internal-url>
   DIRECT_URL=<render-postgres-external-url>
   JWT_SECRET=<your-secret>
   ```
4. Deploy automatically on git push
5. Database schema will be created automatically via postinstall hook

### Vercel (Static/Serverless)
1. Connect your repository to Vercel
2. Set environment variables in Vercel dashboard
3. Deploy automatically on git push

### Self-Hosted
1. Build the application: `npm run build`
2. Start with: `npm run start` (production server)
3. Use PM2 or similar for production

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