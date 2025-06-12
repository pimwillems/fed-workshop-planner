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
- **Workshop Tiles**: Card-based layout with detailed information
- **Calendar View**: Monthly calendar with interactive workshop blocks
- Easy toggle between viewing preferences

### 🎨 **Modern UI/UX**
- **WCAG AA compliant** color scheme for accessibility
- Dark/light mode with system preference detection
- Responsive design for all devices
- Smooth animations and hover effects

### 🔍 **Advanced Filtering**
- Filter by subject category
- Filter by specific date
- Real-time search and filtering
- Clear and intuitive controls

## 🚀 Quick Start

### Prerequisites
- Node.js 18+ 
- PostgreSQL database (Neon recommended)

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
- **Neon** - Serverless PostgreSQL hosting

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
| `DATABASE_URL` | PostgreSQL connection string (pooled) | Yes |
| `DIRECT_URL` | PostgreSQL direct connection for migrations | Yes |
| `JWT_SECRET` | Secret key for JWT token signing | Yes |

## ♿ Accessibility

This application follows **WCAG AA guidelines**:

- **Color Contrast**: 4.5:1+ ratio for all text
- **Keyboard Navigation**: Full keyboard accessibility
- **Screen Readers**: Semantic HTML and ARIA labels
- **Focus Indicators**: Clear visual focus states
- **Reduced Motion**: Respects user motion preferences

## 🚢 Deployment

### Vercel (Recommended)
1. Connect your repository to Vercel
2. Set environment variables in Vercel dashboard
3. Deploy automatically on git push

### Netlify
1. Connect repository to Netlify
2. Set build command: `npm run generate`
3. Set environment variables
4. Deploy

### Self-Hosted
1. Build the application: `npm run build`
2. Start with: `npm run preview`
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