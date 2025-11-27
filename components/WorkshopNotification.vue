<template>
  <Transition name="notification" appear>
    <div
      v-if="show"
      class="notification-overlay"
      @click="dismiss"
    >
      <div class="notification" @click.stop>
        <div class="notification-icon">
          🎉
        </div>
        <div class="notification-content">
          <h3 class="notification-title">New Workshop Added!</h3>
          <p class="notification-message">
            <strong>{{ workshop.title }}</strong> by {{ workshop.teacherName }}
          </p>
          <p class="notification-details">
            <span class="workshop-subject" :class="`subject-${workshop.subject.toLowerCase()}`">
              {{ workshop.subject }}
            </span>
            • {{ formatDate(workshop.date) }}
          </p>
        </div>
        <button class="notification-close" @click="dismiss">
          ✕
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
interface Props {
  show: boolean
  workshop: {
    title: string
    teacherName: string
    subject: string
    date: string
  }
}

defineProps<Props>()

const emit = defineEmits<{
  dismiss: []
}>()

const dismiss = () => {
  emit('dismiss')
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Auto-dismiss after 5 seconds
onMounted(() => {
  setTimeout(() => {
    dismiss()
  }, 5000)
})
</script>

<style scoped>
.notification-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  cursor: pointer;
}

.notification {
  background: var(--bg-primary);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 20px 40px var(--shadow-color);
  border: 2px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 1rem;
  max-width: 500px;
  width: 90%;
  position: relative;
  cursor: default;
  transform: scale(1);
  animation: bounce 0.6s ease-out;
}

.notification-icon {
  font-size: 3rem;
  animation: pulse 2s infinite;
}

.notification-content {
  flex: 1;
}

.notification-title {
  color: var(--text-primary);
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.notification-message {
  color: var(--text-secondary);
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.notification-details {
  color: var(--text-muted);
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.workshop-subject {
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 500;
}

.subject-dev {
  background: var(--color-dev);
  color: var(--color-dev-text);
}

.subject-ux {
  background: var(--color-ux);
  color: var(--color-ux-text);
}

.subject-po {
  background: var(--color-po);
  color: var(--color-po-text);
}

.subject-research {
  background: var(--color-research);
  color: var(--color-research-text);
}

.subject-portfolio {
  background: var(--color-portfolio);
  color: var(--color-portfolio-text);
}

.subject-misc {
  background: var(--color-misc);
  color: var(--color-misc-text);
}

.notification-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-muted);
  cursor: pointer;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.notification-close:hover {
  background: var(--bg-tertiary);
  color: var(--text-primary);
}

/* Animations */
@keyframes bounce {
  0% {
    transform: scale(0) rotate(45deg);
    opacity: 0;
  }
  50% {
    transform: scale(1.1) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: scale(1) rotate(0deg);
    opacity: 1;
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

/* Transition animations */
.notification-enter-active,
.notification-leave-active {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.notification-enter-from {
  opacity: 0;
  transform: scale(0.8) translateY(-50px);
}

.notification-leave-to {
  opacity: 0;
  transform: scale(0.9) translateY(50px);
}
</style>