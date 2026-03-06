<template>
  <div class="container" style="padding: 2rem 1rem;">
    <div style="margin-bottom: 3rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">
          User Management
        </h1>
        <NuxtLink to="/dashboard" class="btn btn-secondary" style="font-size: 0.875rem;">
          ← Back to Dashboard
        </NuxtLink>
      </div>
      <p style="color: var(--text-secondary);">
        Manage all users and reset passwords
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" style="text-align: center; padding: 3rem;">
      <div class="spinner" style="margin: 0 auto 1rem;"></div>
      <p style="color: var(--text-secondary);">Loading users...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" style="padding: 2rem; text-align: center;">
      <p class="error-text" style="margin-bottom: 1rem;">{{ error }}</p>
      <button @click="fetchUsers" class="btn btn-primary">Try Again</button>
    </div>

    <!-- Users Table -->
    <div v-else-if="users.length > 0" style="background: var(--surface); border: 1px solid var(--border); border-radius: 0.5rem; overflow: hidden;">
      <table style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr style="background: var(--surface-dark); border-bottom: 1px solid var(--border);">
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Name</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Email</th>
            <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Role</th>
            <th style="padding: 1rem; text-align: center; font-weight: 600; color: var(--text-primary);">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            style="border-bottom: 1px solid var(--border);"
          >
            <td style="padding: 1rem; color: var(--text-primary);">{{ user.name }}</td>
            <td style="padding: 1rem; color: var(--text-secondary);">{{ user.email }}</td>
            <td style="padding: 1rem;">
              <span
                :style="{
                  display: 'inline-block',
                  padding: '0.25rem 0.75rem',
                  borderRadius: '1rem',
                  fontSize: '0.75rem',
                  fontWeight: '600',
                  textTransform: 'uppercase',
                  backgroundColor: user.role === 'admin' ? 'var(--color-portfolio-dark)' : 'var(--color-dev-dark)',
                  color: 'white'
                }"
              >
                {{ user.role }}
              </span>
            </td>
            <td style="padding: 1rem; text-align: center;">
              <button
                @click="openResetPasswordModal(user)"
                class="btn btn-secondary"
                style="font-size: 0.875rem;"
              >
                Reset Password
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-else style="text-align: center; padding: 3rem;">
      <p style="color: var(--text-secondary);">No users found</p>
    </div>

    <!-- Reset Password Modal -->
    <div
      v-if="showResetModal"
      style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;"
      @click.self="closeResetModal"
    >
      <div class="card" style="max-width: 450px; width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">
            Reset Password
          </h3>
          <button @click="closeResetModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">
            ×
          </button>
        </div>

        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
          Reset password for: <strong>{{ selectedUser?.name }}</strong> ({{ selectedUser?.email }})
        </p>

        <form @submit.prevent="handleResetPassword">
          <div class="form-group">
            <label class="form-label">New Password</label>
            <input
              v-model="newPassword"
              type="text"
              class="form-input"
              required
              placeholder="Enter new password"
              minlength="6"
            />
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
              Minimum 6 characters
            </p>
          </div>

          <div v-if="resetError" style="margin-bottom: 1rem;">
            <p class="error-text" style="font-size: 0.875rem;">
              {{ resetError }}
            </p>
          </div>

          <div v-if="resetSuccess" style="margin-bottom: 1rem;">
            <p style="color: var(--color-po-dark); font-size: 0.875rem;">
              {{ resetSuccess }}
            </p>
          </div>

          <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <button type="button" @click="closeResetModal" class="btn">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="resetLoading">
              <span v-if="!resetLoading">Reset Password</span>
              <span v-else style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <div class="spinner"></div>
                Resetting...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { User } from '~/types'
import { useAuthStore } from '~/store/auth'

definePageMeta({
  middleware: 'auth'
})

const authStore = useAuthStore()

// Redirect if not admin
onMounted(() => {
  if (!authStore.isAdmin) {
    navigateTo('/dashboard')
  }
})

const users = ref<User[]>([])
const loading = ref(false)
const error = ref('')

const showResetModal = ref(false)
const selectedUser = ref<User | null>(null)
const newPassword = ref('')
const resetLoading = ref(false)
const resetError = ref('')
const resetSuccess = ref('')

const fetchUsers = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await $fetch<{ success: boolean; data: { users: User[] } }>('/api/admin/users', {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })

    if (response.success) {
      users.value = response.data.users
    }
  } catch (err: any) {
    console.error('Failed to fetch users:', err)
    error.value = err.data?.message || 'Failed to load users'
  } finally {
    loading.value = false
  }
}

const openResetPasswordModal = (user: User) => {
  selectedUser.value = user
  newPassword.value = ''
  resetError.value = ''
  resetSuccess.value = ''
  showResetModal.value = true
}

const closeResetModal = () => {
  showResetModal.value = false
  selectedUser.value = null
  newPassword.value = ''
  resetError.value = ''
  resetSuccess.value = ''
}

const handleResetPassword = async () => {
  if (!selectedUser.value || resetLoading.value) return

  resetLoading.value = true
  resetError.value = ''
  resetSuccess.value = ''

  try {
    const response = await $fetch(`/api/admin/users/${selectedUser.value.id}/reset-password`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${authStore.token}`
      },
      body: {
        newPassword: newPassword.value
      }
    })

    resetSuccess.value = response.message || 'Password reset successfully!'

    // Close modal after 2 seconds
    setTimeout(() => {
      closeResetModal()
    }, 2000)
  } catch (err: any) {
    console.error('Failed to reset password:', err)
    resetError.value = err.data?.message || 'Failed to reset password'
  } finally {
    resetLoading.value = false
  }
}

// Load users on mount
onMounted(() => {
  fetchUsers()
})

// SEO
useSeoMeta({
  title: 'User Management | FED Learning Hub',
  description: 'Manage users and reset passwords'
})
</script>
