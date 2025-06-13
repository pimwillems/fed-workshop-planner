<template>
  <div class="container" style="max-width: 400px; margin: 4rem auto; padding: 2rem;">
    <div class="card">
      <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.875rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
          Teacher Login
        </h1>
        <p style="color: var(--text-secondary);">
          Sign in to manage your workshops
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

      <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
        <p style="font-size: 0.875rem; color: var(--text-muted); text-align: center; margin-bottom: 1rem;">
          Demo Accounts:
        </p>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <button 
            @click="fillDemoCredentials('admin')"
            class="btn"
            style="flex: 1; font-size: 0.75rem;"
          >
            Admin Demo
          </button>
          <button 
            @click="fillDemoCredentials('teacher')"
            class="btn"
            style="flex: 1; font-size: 0.75rem;"
          >
            Teacher Demo
          </button>
        </div>
      </div>
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

const fillDemoCredentials = (type: 'admin' | 'teacher') => {
  if (type === 'admin') {
    form.email = 'admin@workshop.com'
    form.password = 'admin123'
  } else {
    form.email = 'teacher@workshop.com'
    form.password = 'admin123'
  }
  error.value = ''
}

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
    console.log('Login response user:', data.user)
    authStore.$patch({
      user: data.user,
      token: data.token,
      isAuthenticated: true
    })

    // Store token in cookie
    const tokenCookie = useCookie('auth-token', {
      httpOnly: false,
      secure: false,
      sameSite: 'lax',
      maxAge: 60 * 60 * 24 * 7 // 7 days
    })
    tokenCookie.value = data.token

    // Wait a tick for store to update
    await nextTick()
    
    console.log('Store after login:', {
      user: authStore.user,
      isAuthenticated: authStore.isAuthenticated,
      isTeacher: authStore.isTeacher
    })

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
  title: 'Login | Workshop Planner',
  description: 'Sign in to your teacher account to manage workshops and create new learning sessions.'
})
</script>