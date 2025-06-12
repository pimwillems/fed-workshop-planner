export interface User {
  id: string
  email: string
  name: string
  role: 'teacher' | 'admin'
  createdAt: Date
  updatedAt: Date
}

export type Subject = 'Dev' | 'UX' | 'PO' | 'Research' | 'Portfolio' | 'Misc'

export interface Workshop {
  id: string
  title: string
  description: string
  subject: Subject
  date: string
  teacherId: string
  teacher: User
  createdAt: Date
  updatedAt: Date
}

export interface CreateWorkshopData {
  title: string
  description: string
  subject: Subject
  date: string
}

export interface UpdateWorkshopData extends Partial<CreateWorkshopData> {
  id: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
}

export interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

export interface ApiResponse<T = any> {
  success: boolean
  data?: T
  message?: string
  error?: string
}

export interface WorkshopFilters {
  subject?: Subject
  date?: string
  teacherId?: string
}