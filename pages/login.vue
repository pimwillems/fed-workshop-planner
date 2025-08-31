<template>
  <div class="container" style="max-width: 400px; margin: 4rem auto; padding: 2rem;">
    <div class="card">
      <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
          FED Teacher Login
        </h1>
        <p style="color: var(--text-secondary);">
          Sign in to manage your FED workshops
        </p>
      </div>

      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label class="form-label">Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            class="form-input" 
            required
            placeholder="Enter your email address"
          />
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            class="form-input" 
            required
            placeholder="Enter your password"
          />
        </div>

        <div v-if="error" style="margin-bottom: 1rem;">
          <p class="error-text" style="font-size: 0.875rem; text-align: center;">
            {{ error }}
          </p>
        </div>

        <button 
          type="submit" 
          class="btn btn-primary" 
          style="width: 100%; margin-bottom: 1rem; position: relative;"
          :disabled="loading"
        >
          <span v-if="!loading">Sign In</span>
          <span v-else style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <div class="spinner"></div>
            Signing in...
          </span>
        </button>
      </form>

    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/store/auth'

definePageMeta({
  layout: 'default'
})

const authStore = useAuthStore()

const loading = ref(false)
const error = ref('')

const form = reactive({
  email: '',
  password: ''
})

const handleSubmit = async () => {
  if (loading.value) return
  
  loading.value = true
  error.value = ''

  try {
    // Direct API call for login
    const { data } = await $fetch('/api/auth/login', {
      method: 'POST',
      body: {
        email: form.email,
        password: form.password
      }
    })

    // Update store manually with reactive assignment
    authStore.$patch({
      user: data.user,
      token: data.token,
      isAuthenticated: true
    })

    // Store token in cookie
    const tokenCookie = useCookie('auth-token', {
      httpOnly: true,
      secure: true,
      sameSite: 'strict',
      maxAge: 60 * 60 * 24 * 7 // 7 days
    })
    tokenCookie.value = data.token

    // Wait a tick for store to update
    await nextTick()

    await navigateTo('/dashboard')
  } catch (err: any) {
    console.error('Auth error:', err)
    error.value = err.data?.message || err.message || 'Something went wrong'
  } finally {
    loading.value = false
  }
}

// Redirect if already authenticated
onMounted(() => {
  if (authStore.isAuthenticated) {
    navigateTo('/dashboard')
  }
})

// SEO
useSeoMeta({
  title: 'Login | FED Learning Hub',
  description: 'Sign in to your teacher account to manage FED workshops and create new learning sessions.'
})
</script>