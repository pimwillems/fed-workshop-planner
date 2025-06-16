<template>
  <div class="change-password-page">
    <div class="container">
      <h1>Change Password</h1>
      
      <div class="form-container">
        <form @submit.prevent="changePassword" class="change-password-form">
          <div class="form-group">
            <label for="currentPassword">Current Password</label>
            <input
              id="currentPassword"
              v-model="currentPassword"
              type="password"
              required
              :disabled="isLoading"
              class="form-input"
              placeholder="Enter your current password"
            />
          </div>

          <div class="form-group">
            <label for="newPassword">New Password</label>
            <input
              id="newPassword"
              v-model="newPassword"
              type="password"
              required
              minlength="6"
              :disabled="isLoading"
              class="form-input"
              placeholder="Enter your new password (min 6 characters)"
            />
          </div>

          <div class="form-group">
            <label for="confirmPassword">Confirm New Password</label>
            <input
              id="confirmPassword"
              v-model="confirmPassword"
              type="password"
              required
              :disabled="isLoading"
              class="form-input"
              placeholder="Confirm your new password"
            />
          </div>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>

          <div v-if="success" class="success-message">
            {{ success }}
          </div>

          <div class="form-actions">
            <button type="submit" :disabled="isLoading || !isFormValid" class="btn btn-primary">
              <span v-if="isLoading" class="loading-spinner"></span>
              {{ isLoading ? 'Changing...' : 'Change Password' }}
            </button>
            <NuxtLink to="/dashboard" class="btn btn-secondary">
              Cancel
            </NuxtLink>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ChangePassword',
  middleware: 'auth',
  data() {
    return {
      currentPassword: '',
      newPassword: '',
      confirmPassword: '',
      isLoading: false,
      error: '',
      success: ''
    }
  },
  computed: {
    isFormValid() {
      return this.currentPassword && 
             this.newPassword && 
             this.confirmPassword && 
             this.newPassword === this.confirmPassword &&
             this.newPassword.length >= 6
    }
  },
  methods: {
    async changePassword() {
      this.error = ''
      this.success = ''

      if (this.newPassword !== this.confirmPassword) {
        this.error = 'New passwords do not match'
        return
      }

      if (this.newPassword.length < 6) {
        this.error = 'New password must be at least 6 characters long'
        return
      }

      this.isLoading = true

      try {
        const response = await $fetch('/api/auth/change-password', {
          method: 'POST',
          body: {
            currentPassword: this.currentPassword,
            newPassword: this.newPassword
          }
        })

        if (response.success) {
          this.success = 'Password changed successfully!'
          this.currentPassword = ''
          this.newPassword = ''
          this.confirmPassword = ''
          
          setTimeout(() => {
            this.$router.push('/dashboard')
          }, 2000)
        }
      } catch (error) {
        console.error('Change password error:', error)
        this.error = error.data?.message || 'Failed to change password. Please try again.'
      } finally {
        this.isLoading = false
      }
    }
  }
}
</script>

<style scoped>
.change-password-page {
  min-height: 100vh;
  background: var(--bg-color);
  color: var(--text-color);
  padding: 2rem 0;
}

.container {
  max-width: 500px;
  margin: 0 auto;
  padding: 0 1rem;
}

h1 {
  text-align: center;
  margin-bottom: 2rem;
  color: var(--text-color);
}

.form-container {
  background: var(--card-bg);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
}

.change-password-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: var(--text-color);
}

.form-input {
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  background: var(--input-bg);
  color: var(--text-color);
  transition: border-color 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color);
}

.form-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  color: var(--error-color);
  background: var(--error-bg);
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid var(--error-color);
  font-size: 0.9rem;
}

.success-message {
  color: var(--success-color);
  background: var(--success-bg);
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid var(--success-color);
  font-size: 0.9rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  min-height: 44px;
}

.btn-primary {
  background: var(--primary-color);
  color: white;
  flex: 1;
}

.btn-primary:hover:not(:disabled) {
  background: var(--primary-hover);
  transform: translateY(-1px);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: var(--secondary-bg);
  color: var(--text-color);
  border: 2px solid var(--border-color);
}

.btn-secondary:hover {
  background: var(--secondary-hover);
  transform: translateY(-1px);
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .form-container {
    padding: 1.5rem;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>