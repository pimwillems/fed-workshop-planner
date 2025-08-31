<template>
  <div>
    <nav class="nav">
      <div class="container nav-content">
        <NuxtLink to="/" class="nav-brand"> FED Learning Hub </NuxtLink>

        <ul class="nav-links">
          <li>
            <NuxtLink to="/" class="nav-link" active-class="active">
              Schedule
            </NuxtLink>
          </li>
          <li v-if="authStore.isAuthenticated">
            <NuxtLink to="/dashboard" class="nav-link" active-class="active">
              Dashboard
            </NuxtLink>
          </li>
          <li v-if="!authStore.isAuthenticated">
            <NuxtLink to="/login" class="nav-link" active-class="active">
              Login
            </NuxtLink>
          </li>
          <li v-if="authStore.isAuthenticated">
            <button
              @click="handleLogout"
              class="nav-link"
              style="background: none; border: none; cursor: pointer"
            >
              Logout
            </button>
          </li>
          <li>
            <button
              @click="toggleColorMode"
              class="btn"
              style="padding: 0.5rem"
            >
              <span v-if="$colorMode.value === 'dark'">☀️</span>
              <span v-else>🌙</span>
            </button>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Beta Warning Banner -->
    <div class="beta-warning">
      <div class="container">
        <span class="beta-warning-text">
          ⚠️ This is a beta release. Bugs can be reported as issues on the 
          <a href="https://github.com/anthropics/claude-code/issues" target="_blank" rel="noopener noreferrer" class="beta-warning-link">Github page</a>.
        </span>
      </div>
    </div>

    <main>
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/store/auth";
const { $colorMode } = useNuxtApp();

const authStore = useAuthStore();

const toggleColorMode = () => {
  $colorMode.preference = $colorMode.value === "dark" ? "light" : "dark";
};

const handleLogout = async () => {
  await authStore.logout();
};

onMounted(async () => {
  await authStore.checkAuth();
});
</script>
