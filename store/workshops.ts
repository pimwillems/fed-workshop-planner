import { defineStore } from 'pinia'
import type { Workshop, CreateWorkshopData, UpdateWorkshopData, WorkshopFilters, Subject } from '~/types'

export const useWorkshopsStore = defineStore('workshops', {
  state: () => ({
    workshops: [] as Workshop[],
    loading: false,
    error: null as string | null,
    filters: {
      subject: undefined,
      date: undefined,
      teacherId: undefined
    } as WorkshopFilters
  }),

  getters: {
    filteredWorkshops(): Workshop[] {
      let filtered = [...this.workshops]

      if (this.filters.subject) {
        filtered = filtered.filter(w => w.subject === this.filters.subject)
      }

      if (this.filters.date) {
        filtered = filtered.filter(w => w.date === this.filters.date)
      }

      if (this.filters.teacherId) {
        filtered = filtered.filter(w => w.teacherId === this.filters.teacherId)
      }

      return filtered.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime())
    },

    workshopsBySubject(): Record<Subject, Workshop[]> {
      const subjects: Subject[] = ['Dev', 'UX', 'PO', 'Research', 'Portfolio', 'Misc']
      const result: Record<Subject, Workshop[]> = {} as Record<Subject, Workshop[]>

      subjects.forEach(subject => {
        result[subject] = this.filteredWorkshops.filter(w => w.subject === subject)
      })

      return result
    },

    workshopsByDate(): Record<string, Workshop[]> {
      const result: Record<string, Workshop[]> = {}

      this.filteredWorkshops.forEach(workshop => {
        if (!result[workshop.date]) {
          result[workshop.date] = []
        }
        result[workshop.date].push(workshop)
      })

      return result
    },

    getMyWorkshops: (state) => (userId: string | undefined) => {
      if (!userId) return []
      return state.workshops.filter(w => w.teacherId === userId)
    }
  },

  actions: {
    async fetchWorkshops() {
      this.loading = true
      this.error = null

      try {
        const { data } = await $fetch<{ workshops: Workshop[] }>('/api/workshops')
        this.workshops = data.workshops
      } catch (error: any) {
        console.error('Failed to fetch workshops:', error)
        this.error = error.data?.message || 'Failed to fetch workshops'
      } finally {
        this.loading = false
      }
    },

    async createWorkshop(workshopData: CreateWorkshopData) {
      try {
        const { useAuthStore } = await import('~/store/auth')
        const authStore = useAuthStore()
        
        // Get token from store or cookie as fallback
        let token = authStore.token
        if (!token) {
          const tokenCookie = useCookie('auth-token')
          token = tokenCookie.value
        }
        
        if (!token) {
          throw new Error('No authentication token available')
        }
        
        const { data } = await $fetch<{ workshop: Workshop }>('/api/workshops', {
          method: 'POST',
          body: workshopData,
          headers: {
            Authorization: `Bearer ${token}`
          }
        })

        this.workshops.push(data.workshop)
        return { success: true, workshop: data.workshop }
      } catch (error: any) {
        console.error('Failed to create workshop:', error)
        return { 
          success: false, 
          message: error.data?.message || 'Failed to create workshop' 
        }
      }
    },

    async updateWorkshop(workshopData: UpdateWorkshopData) {
      try {
        const { useAuthStore } = await import('~/store/auth')
        const authStore = useAuthStore()
        
        // Get token from store or cookie as fallback
        let token = authStore.token
        if (!token) {
          const tokenCookie = useCookie('auth-token')
          token = tokenCookie.value
        }
        
        if (!token) {
          throw new Error('No authentication token available')
        }
        
        const { data } = await $fetch<{ workshop: Workshop }>(`/api/workshops/${workshopData.id}`, {
          method: 'PUT',
          body: workshopData,
          headers: {
            Authorization: `Bearer ${token}`
          }
        })

        const index = this.workshops.findIndex(w => w.id === workshopData.id)
        if (index !== -1) {
          this.workshops[index] = data.workshop
        }

        return { success: true, workshop: data.workshop }
      } catch (error: any) {
        console.error('Failed to update workshop:', error)
        return { 
          success: false, 
          message: error.data?.message || 'Failed to update workshop' 
        }
      }
    },

    async deleteWorkshop(workshopId: string) {
      try {
        const { useAuthStore } = await import('~/store/auth')
        const authStore = useAuthStore()
        
        // Get token from store or cookie as fallback
        let token = authStore.token
        if (!token) {
          const tokenCookie = useCookie('auth-token')
          token = tokenCookie.value
        }
        
        if (!token) {
          throw new Error('No authentication token available')
        }
        
        await $fetch(`/api/workshops/${workshopId}`, {
          method: 'DELETE',
          headers: {
            Authorization: `Bearer ${token}`
          }
        })

        this.workshops = this.workshops.filter(w => w.id !== workshopId)
        return { success: true }
      } catch (error: any) {
        console.error('Failed to delete workshop:', error)
        return { 
          success: false, 
          message: error.data?.message || 'Failed to delete workshop' 
        }
      }
    },

    setFilters(newFilters: Partial<WorkshopFilters>) {
      this.filters = { ...this.filters, ...newFilters }
    },

    clearFilters() {
      this.filters = {
        subject: undefined,
        date: undefined,
        teacherId: undefined
      }
    }
  }
})