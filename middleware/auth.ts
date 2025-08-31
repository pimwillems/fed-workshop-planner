export default defineNuxtRouteMiddleware(async (to) => {
  const { useAuthStore } = await import('~/store/auth')
  const authStore = useAuthStore()
  
  // Check if user is authenticated via cookie if store is not populated
  if (!authStore.isAuthenticated) {
    const tokenCookie = useCookie('auth-token')
    if (tokenCookie.value) {
      try {
        const { data } = await $fetch('/api/auth/me', {
          headers: {
            Authorization: `Bearer ${tokenCookie.value}`
          }
        })
        
        authStore.$patch({
          user: data.user,
          token: tokenCookie.value,
          isAuthenticated: true
        })
      } catch (error) {
        // Invalid token, redirect to login
        tokenCookie.value = null
        return navigateTo('/login')
      }
    } else {
      return navigateTo('/login')
    }
  }
  
  // Check if user has proper role - use direct comparison instead of getter
  const userRole = authStore.user?.role
  const isTeacherOrAdmin = userRole === 'teacher' || userRole === 'admin'
  
  if (!isTeacherOrAdmin) {
    throw createError({
      statusCode: 403,
      statusMessage: 'Access denied. Teachers only.'
    })
  }
})