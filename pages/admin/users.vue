<template>
  <div class="container" style="padding: 2rem 1rem;">
    <div style="margin-bottom: 3rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">
          User Management
        </h1>
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
          <button @click="openAddModal" class="btn btn-primary" style="font-size: 0.875rem;">
            + Add User
          </button>
          <NuxtLink to="/dashboard" class="btn btn-secondary" style="font-size: 0.875rem;">
            ← Back to Dashboard
          </NuxtLink>
        </div>
      </div>
      <p style="color: var(--text-secondary);">
        Manage all users, edit details and reset passwords
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
              <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                <button @click="openEditModal(user)" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">
                  Edit
                </button>
                <button @click="openResetPasswordModal(user)" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">
                  Password
                </button>
                <button
                  @click="openDeleteConfirm(user)"
                  class="btn"
                  :disabled="user.id === authStore.user?.id"
                  style="font-size: 0.8rem; padding: 0.4rem 0.75rem; background: var(--color-misc-dark); color: white; border-color: var(--color-misc-dark);"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-else style="text-align: center; padding: 3rem;">
      <p style="color: var(--text-secondary);">No users found</p>
    </div>

    <!-- Add User Modal -->
    <div
      v-if="showAddModal"
      style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;"
      @click.self="closeAddModal"
    >
      <div class="card" style="max-width: 480px; width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">Add User</h3>
          <button @click="closeAddModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">×</button>
        </div>

        <form @submit.prevent="handleAddUser">
          <div class="form-group">
            <label class="form-label">Name</label>
            <input v-model="addForm.name" type="text" class="form-input" required placeholder="Full name" minlength="2" />
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <input v-model="addForm.email" type="email" class="form-input" required placeholder="email@example.com" />
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <input v-model="addForm.password" type="text" class="form-input" required placeholder="Password" />
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
              Min 8 characters, must include uppercase, lowercase and a number
            </p>
          </div>

          <div class="form-group">
            <label class="form-label">Role</label>
            <select v-model="addForm.role" class="form-input">
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div v-if="addError" style="margin-bottom: 1rem;">
            <p class="error-text" style="font-size: 0.875rem;">{{ addError }}</p>
          </div>

          <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <button type="button" @click="closeAddModal" class="btn">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="addLoading">
              <span v-if="!addLoading">Add User</span>
              <span v-else style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="spinner"></div> Adding...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div
      v-if="showEditModal"
      style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;"
      @click.self="closeEditModal"
    >
      <div class="card" style="max-width: 480px; width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">Edit User</h3>
          <button @click="closeEditModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">×</button>
        </div>

        <form @submit.prevent="handleEditUser">
          <div class="form-group">
            <label class="form-label">Name</label>
            <input v-model="editForm.name" type="text" class="form-input" required placeholder="Full name" minlength="2" />
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <input v-model="editForm.email" type="email" class="form-input" required placeholder="email@example.com" />
          </div>

          <div class="form-group">
            <label class="form-label">Role</label>
            <select v-model="editForm.role" class="form-input">
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div v-if="editError" style="margin-bottom: 1rem;">
            <p class="error-text" style="font-size: 0.875rem;">{{ editError }}</p>
          </div>

          <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <button type="button" @click="closeEditModal" class="btn">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="editLoading">
              <span v-if="!editLoading">Save Changes</span>
              <span v-else style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="spinner"></div> Saving...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Reset Password Modal -->
    <div
      v-if="showResetModal"
      style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;"
      @click.self="closeResetModal"
    >
      <div class="card" style="max-width: 450px; width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">Reset Password</h3>
          <button @click="closeResetModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">×</button>
        </div>

        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
          Reset password for: <strong>{{ selectedUser?.name }}</strong> ({{ selectedUser?.email }})
        </p>

        <form @submit.prevent="handleResetPassword">
          <div class="form-group">
            <label class="form-label">New Password</label>
            <input v-model="newPassword" type="text" class="form-input" required placeholder="Enter new password" />
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
              Min 8 characters, must include uppercase, lowercase and a number
            </p>
          </div>

          <div v-if="resetError" style="margin-bottom: 1rem;">
            <p class="error-text" style="font-size: 0.875rem;">{{ resetError }}</p>
          </div>

          <div v-if="resetSuccess" style="margin-bottom: 1rem;">
            <p style="color: var(--color-po-dark); font-size: 0.875rem;">{{ resetSuccess }}</p>
          </div>

          <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <button type="button" @click="closeResetModal" class="btn">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="resetLoading">
              <span v-if="!resetLoading">Reset Password</span>
              <span v-else style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <div class="spinner"></div> Resetting...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteConfirm"
      style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;"
      @click.self="closeDeleteConfirm"
    >
      <div class="card" style="max-width: 420px; width: 100%;">
        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0 0 1rem;">Delete User</h3>
        <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">
          Are you sure you want to delete <strong>{{ selectedUser?.name }}</strong>?
        </p>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem;">
          This will also delete all their workshops. This action cannot be undone.
        </p>

        <div v-if="deleteError" style="margin-bottom: 1rem;">
          <p class="error-text" style="font-size: 0.875rem;">{{ deleteError }}</p>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
          <button type="button" @click="closeDeleteConfirm" class="btn">Cancel</button>
          <button
            @click="handleDeleteUser"
            class="btn"
            :disabled="deleteLoading"
            style="background: var(--color-misc-dark); color: white; border-color: var(--color-misc-dark);"
          >
            <span v-if="!deleteLoading">Delete User</span>
            <span v-else style="display: flex; align-items: center; gap: 0.5rem;">
              <div class="spinner"></div> Deleting...
            </span>
          </button>
        </div>
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

onMounted(() => {
  if (!authStore.isAdmin) {
    navigateTo('/dashboard')
  }
})

const users = ref<User[]>([])
const loading = ref(false)
const error = ref('')
const selectedUser = ref<User | null>(null)

// Add user
const showAddModal = ref(false)
const addForm = ref({ name: '', email: '', password: '', role: 'teacher' })
const addLoading = ref(false)
const addError = ref('')

// Edit user
const showEditModal = ref(false)
const editForm = ref({ name: '', email: '', role: 'teacher' })
const editLoading = ref(false)
const editError = ref('')

// Reset password
const showResetModal = ref(false)
const newPassword = ref('')
const resetLoading = ref(false)
const resetError = ref('')
const resetSuccess = ref('')

// Delete user
const showDeleteConfirm = ref(false)
const deleteLoading = ref(false)
const deleteError = ref('')

const fetchUsers = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await $fetch<{ success: boolean; data: { users: User[] } }>('/api/admin/users', {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    if (response.success) {
      users.value = response.data.users
    }
  } catch (err: any) {
    error.value = err.data?.message || 'Failed to load users'
  } finally {
    loading.value = false
  }
}

// Add user
const openAddModal = () => {
  addForm.value = { name: '', email: '', password: '', role: 'teacher' }
  addError.value = ''
  showAddModal.value = true
}

const closeAddModal = () => {
  showAddModal.value = false
  addError.value = ''
}

const handleAddUser = async () => {
  if (addLoading.value) return
  addLoading.value = true
  addError.value = ''

  try {
    const response = await $fetch<{ success: boolean; data: { user: User }; message: string }>('/api/admin/users', {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: addForm.value
    })

    if (response.success) {
      users.value.push(response.data.user)
      users.value.sort((a, b) => a.name.localeCompare(b.name))
      closeAddModal()
    }
  } catch (err: any) {
    addError.value = err.data?.message || 'Failed to create user'
  } finally {
    addLoading.value = false
  }
}

// Edit user
const openEditModal = (user: User) => {
  selectedUser.value = user
  editForm.value = { name: user.name, email: user.email, role: user.role }
  editError.value = ''
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  selectedUser.value = null
  editError.value = ''
}

const handleEditUser = async () => {
  if (!selectedUser.value || editLoading.value) return
  editLoading.value = true
  editError.value = ''

  try {
    const response = await $fetch<{ success: boolean; data: { user: User } }>(`/api/admin/users/${selectedUser.value.id}`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: editForm.value
    })

    if (response.success) {
      const index = users.value.findIndex(u => u.id === selectedUser.value!.id)
      if (index !== -1) users.value[index] = response.data.user
      users.value.sort((a, b) => a.name.localeCompare(b.name))
      closeEditModal()
    }
  } catch (err: any) {
    editError.value = err.data?.message || 'Failed to update user'
  } finally {
    editLoading.value = false
  }
}

// Reset password
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
    const response = await $fetch<{ success: boolean; message: string }>(`/api/admin/users/${selectedUser.value.id}/reset-password`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: { newPassword: newPassword.value }
    })

    resetSuccess.value = response.message || 'Password reset successfully!'
    setTimeout(() => closeResetModal(), 2000)
  } catch (err: any) {
    resetError.value = err.data?.message || 'Failed to reset password'
  } finally {
    resetLoading.value = false
  }
}

// Delete user
const openDeleteConfirm = (user: User) => {
  selectedUser.value = user
  deleteError.value = ''
  showDeleteConfirm.value = true
}

const closeDeleteConfirm = () => {
  showDeleteConfirm.value = false
  selectedUser.value = null
  deleteError.value = ''
}

const handleDeleteUser = async () => {
  if (!selectedUser.value || deleteLoading.value) return
  deleteLoading.value = true
  deleteError.value = ''

  try {
    await $fetch(`/api/admin/users/${selectedUser.value.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    users.value = users.value.filter(u => u.id !== selectedUser.value!.id)
    closeDeleteConfirm()
  } catch (err: any) {
    deleteError.value = err.data?.message || 'Failed to delete user'
  } finally {
    deleteLoading.value = false
  }
}

onMounted(() => {
  fetchUsers()
})

useSeoMeta({
  title: 'User Management | FED Learning Hub',
  description: 'Manage users, edit details and reset passwords'
})
</script>
