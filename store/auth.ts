import { defineStore } from 'pinia'
import type { User, LoginCredentials, RegisterData, AuthState } from '~/types'

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),

  getters: {
    isTeacher(): boolean {
      return this.user?.role === 'teacher' || this.user?.role === 'admin'
    },
    isAdmin(): boolean {
      return this.user?.role === 'admin'
    }
  },

  actions: {
    async login(credentials: LoginCredentials) {
      try {
        const { data } = await $fetch<{ user: User; token: string }>('/api/auth/login', {
          method: 'POST',
          body: credentials
        })

        this.user = data.user
        this.token = data.token
        this.isAuthenticated = true

        // Store token in cookie for persistence
        const tokenCookie = useCookie('auth-token', {
          httpOnly: false,
          secure: true,
          sameSite: 'lax',
          maxAge: 60 * 60 * 24 * 7 // 7 days
        })
        tokenCookie.value = data.token

        await navigateTo('/dashboard')
        return { success: true }
      } catch (error: any) {
        console.error('Login error:', error)
        return { 
          success: false, 
          message: error.data?.message || 'Login failed' 
        }
      }
    },

    async register(userData: RegisterData) {
      try {
        const { data } = await $fetch<{ user: User; token: string }>('/api/auth/register', {
          method: 'POST',
          body: userData
        })

        this.user = data.user
        this.token = data.token
        this.isAuthenticated = true

        // Store token in cookie for persistence
        const tokenCookie = useCookie('auth-token')
        tokenCookie.value = data.token

        await navigateTo('/dashboard')
        return { success: true }
      } catch (error: any) {
        console.error('Registration error:', error)
        return { 
          success: false, 
          message: error.data?.message || 'Registration failed' 
        }
      }
    },

    async logout() {
      // Clear store state
      this.$patch({
        user: null,
        token: null,
        isAuthenticated: false
      })

      // Clear cookie
      const tokenCookie = useCookie('auth-token')
      tokenCookie.value = null

      // Force navigation to homepage with page refresh
      if (process.client) {
        window.location.href = '/'
      } else {
        await navigateTo('/')
      }
    },

    async checkAuth() {
      const tokenCookie = useCookie('auth-token')
      const token = tokenCookie.value

      if (!token) {
        return false
      }

      try {
        const { data } = await $fetch<{ user: User }>('/api/auth/me', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })

        this.user = data.user
        this.token = token
        this.isAuthenticated = true
        return true
      } catch (error) {
        console.error('Auth check failed:', error)
        // Clear invalid token
        tokenCookie.value = null
        return false
      }
    },

    async refreshUser() {
      if (!this.token) return

      try {
        const { data } = await $fetch<{ user: User }>('/api/auth/me', {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        })

        this.user = data.user
      } catch (error) {
        console.error('Failed to refresh user:', error)
        await this.logout()
      }
    }
  }
})