<template>
  <div class="container" style="padding: 2rem 1rem;">
    <div style="margin-bottom: 3rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">
          FED Teacher Dashboard
        </h1>
        <NuxtLink to="/change-password" class="btn btn-secondary" style="font-size: 0.875rem;">
          Change Password
        </NuxtLink>
      </div>
      <p style="color: var(--text-secondary);">
        Welcome back, {{ authStore.user?.name }}! Manage your FED workshops here.
      </p>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
      <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin: 0;">
        My FED Workshops
      </h2>
      <button @click="showCreateForm = true" class="btn btn-primary">
        + Create FED Workshop
      </button>
    </div>

    <!-- Create/Edit Workshop Modal -->
    <div 
      v-if="showCreateForm || editingWorkshop" 
      style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;"
      @click.self="closeModal"
    >
      <div class="card" style="max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">
            {{ editingWorkshop ? 'Edit FED Workshop' : 'Create New FED Workshop' }}
          </h3>
          <button @click="closeModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">
            ×
          </button>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="form-group">
            <label class="form-label">Title</label>
            <input 
              v-model="workshopForm.title" 
              type="text" 
              class="form-input" 
              required
              placeholder="FED Workshop title"
            />
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea 
              v-model="workshopForm.description" 
              class="form-input" 
              rows="4"
              required
              placeholder="Describe what this FED workshop covers..."
              style="resize: vertical; min-height: 100px;"
            ></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Subject</label>
            <select v-model="workshopForm.subject" class="form-select" required>
              <option value="">Select a subject</option>
              <option value="Dev">Development</option>
              <option value="UX">UX Design</option>
              <option value="PO">Professional Skills</option>
              <option value="Research">Research</option>
              <option value="Portfolio">Portfolio</option>
              <option value="Misc">Miscellaneous</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Date</label>
            <input 
              v-model="workshopForm.date" 
              type="date" 
              class="form-input" 
              required
              :min="new Date().toISOString().split('T')[0]"
            />
          </div>

          <div v-if="formError" style="margin-bottom: 1rem;">
            <p class="error-text" style="font-size: 0.875rem;">
              {{ formError }}
            </p>
          </div>

          <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <button type="button" @click="closeModal" class="btn">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="formLoading" style="position: relative;">
              <span v-if="!formLoading">{{ editingWorkshop ? 'Update' : 'Create' }}</span>
              <span v-else style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <div class="spinner"></div>
                {{ editingWorkshop ? 'Updating...' : 'Creating...' }}
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="workshopsStore.loading" style="text-align: center; padding: 3rem;">
      <p style="color: var(--text-secondary);">Loading your workshops...</p>
    </div>

    <div v-else-if="myWorkshops.length === 0" style="text-align: center; padding: 3rem;">
      <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 1.5rem;">
        You haven't created any FED workshops yet.
      </p>
      <button @click="showCreateForm = true" class="btn btn-primary">
        Create Your First FED Workshop
      </button>
    </div>

    <div v-else class="grid grid-2">
      <div 
        v-for="workshop in myWorkshops" 
        :key="workshop.id" 
        class="card"
        :class="`subject-${workshop.subject.toLowerCase()}`"
      >
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
          <span 
            class="subject-badge"
            :style="{
              display: 'inline-block',
              padding: '0.25rem 0.75rem',
              borderRadius: '1rem',
              fontSize: '0.75rem',
              fontWeight: '600',
              textTransform: 'uppercase',
              letterSpacing: '0.05em',
              backgroundColor: getSubjectColor(workshop.subject),
              color: getSubjectTextColor(workshop.subject)
            }"
          >
            {{ workshop.subject }}
          </span>
          <div style="display: flex; gap: 0.5rem;">
            <button 
              @click="startEdit(workshop)"
              style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0.25rem;"
              title="Edit workshop"
            >
              ✏️
            </button>
            <button 
              @click="handleDelete(workshop.id)"
              style="background: none; border: none; color: var(--color-portfolio-dark); cursor: pointer; padding: 0.25rem; display: flex; align-items: center; gap: 0.25rem;"
              title="Delete workshop"
              :disabled="deleteLoading === workshop.id"
            >
              <div v-if="deleteLoading === workshop.id" class="spinner" style="width: 12px; height: 12px; border-width: 1px; border-color: var(--color-portfolio-dark); border-top-color: transparent;"></div>
              <span v-else>🗑️</span>
            </button>
          </div>
        </div>

        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-primary);">
          {{ sanitizeText(workshop.title) }}
        </h3>

        <p style="color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5;">
          {{ sanitizeText(workshop.description) }}
        </p>

        <div style="color: var(--text-muted); font-size: 0.875rem;">
          📅 {{ formatDate(workshop.date) }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Subject, Workshop, CreateWorkshopData } from '~/types'
import { useAuthStore } from '~/store/auth'
import { useWorkshopsStore } from '~/store/workshops'
import { sanitizeText } from '~/utils/sanitization'

definePageMeta({
  middleware: 'auth'
})

const authStore = useAuthStore()
const workshopsStore = useWorkshopsStore()

const showCreateForm = ref(false)
const editingWorkshop = ref<Workshop | null>(null)
const formLoading = ref(false)
const formError = ref('')
const deleteLoading = ref<string | null>(null)

const workshopForm = reactive({
  title: '',
  description: '',
  subject: '' as Subject | '',
  date: ''
})

const myWorkshops = computed(() => workshopsStore.getMyWorkshops(authStore.user?.id))

const resetForm = () => {
  workshopForm.title = ''
  workshopForm.description = ''
  workshopForm.subject = ''
  workshopForm.date = ''
  formError.value = ''
}

const closeModal = () => {
  showCreateForm.value = false
  editingWorkshop.value = null
  resetForm()
}

const startEdit = (workshop: Workshop) => {
  editingWorkshop.value = workshop
  workshopForm.title = workshop.title
  workshopForm.description = workshop.description
  workshopForm.subject = workshop.subject
  workshopForm.date = workshop.date
  formError.value = ''
}

const handleSubmit = async () => {
  if (formLoading.value) return
  
  formLoading.value = true
  formError.value = ''

  try {
    let result

    if (editingWorkshop.value) {
      result = await workshopsStore.updateWorkshop({
        id: editingWorkshop.value.id,
        title: workshopForm.title,
        description: workshopForm.description,
        subject: workshopForm.subject as Subject,
        date: workshopForm.date
      })
    } else {
      result = await workshopsStore.createWorkshop({
        title: workshopForm.title,
        description: workshopForm.description,
        subject: workshopForm.subject as Subject,
        date: workshopForm.date
      })
    }

    if (result.success) {
      closeModal()
    } else {
      formError.value = result.message || 'Something went wrong'
    }
  } catch (error: any) {
    formError.value = error.message || 'Something went wrong'
  } finally {
    formLoading.value = false
  }
}

const handleDelete = async (workshopId: string) => {
  if (!confirm('Are you sure you want to delete this workshop?')) {
    return
  }

  deleteLoading.value = workshopId

  try {
    const result = await workshopsStore.deleteWorkshop(workshopId)
    
    if (!result.success) {
      alert(result.message || 'Failed to delete workshop')
    }
  } catch (error: any) {
    alert(error.message || 'Failed to delete workshop')
  } finally {
    deleteLoading.value = null
  }
}

const getSubjectColor = (subject: Subject): string => {
  const colors = {
    Dev: 'var(--color-dev-dark)',
    UX: 'var(--color-ux-dark)',
    PO: 'var(--color-po-dark)',
    Research: 'var(--color-research-dark)',
    Portfolio: 'var(--color-portfolio-dark)',
    Misc: 'var(--color-misc-dark)'
  }
  return colors[subject] || 'var(--color-misc-dark)'
}

const getSubjectTextColor = (subject: Subject): string => {
  const colors = {
    Dev: 'white',
    UX: 'white',
    PO: 'white',
    Research: 'var(--color-research-text)',
    Portfolio: 'white',
    Misc: 'var(--color-misc-text)'
  }
  return colors[subject] || 'white'
}

const formatDate = (dateString: string): string => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Load workshops on component mount
onMounted(async () => {
  await workshopsStore.fetchWorkshops()
})

// SEO
useSeoMeta({
  title: 'Dashboard | FED Learning Hub',
  description: 'Manage your FED workshops, create new learning sessions, and track your teaching schedule.'
})
</script>