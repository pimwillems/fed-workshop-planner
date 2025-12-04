<template>
  <ClientOnly>
    <button
      @click="openCalendarOptions"
      class="btn btn-calendar"
      type="button"
    >
      📅 Add to Calendar
    </button>

    <!-- Modal for calendar options -->
    <div
      v-if="showModal"
      @click.self="closeModal"
      style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem;"
    >
      <div style="background: var(--bg-primary); border-radius: 0.5rem; padding: 1.5rem; max-width: 400px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">
            Add to Calendar
          </h3>
          <button
            @click="closeModal"
            style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); padding: 0; line-height: 1;"
          >
            ×
          </button>
        </div>

        <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
          <p style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">{{ title }}</p>
          <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0;">{{ formatDate(startDate) }}</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
          <a
            :href="googleCalendarUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-calendar-option"
            @click="closeModal"
          >
            Google Calendar
          </a>
          <a
            :href="icsDownloadUrl"
            download="workshop.ics"
            class="btn btn-calendar-option"
            @click="closeModal"
          >
            Apple Calendar (iCal)
          </a>
          <a
            :href="office365CalendarUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-calendar-option"
            @click="closeModal"
          >
            Microsoft 365
          </a>
        </div>
      </div>
    </div>
  </ClientOnly>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface Props {
  title: string
  description: string
  startDate: string
  location?: string
}

const props = withDefaults(defineProps<Props>(), {
  location: 'FED Workshop'
})

const showModal = ref(false)

const openCalendarOptions = () => {
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const formatDate = (dateString: string): string => {
  const date = new Date(`${dateString}T09:00:00`)
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Format date for calendar URLs (YYYYMMDD format)
const formatDateForCalendar = (dateString: string): string => {
  const date = new Date(`${dateString}T09:00:00`)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}${month}${day}`
}

// Format datetime for ICS (YYYYMMDDTHHMMSS format)
const formatDateTimeForICS = (dateString: string, time: string = '090000'): string => {
  return `${formatDateForCalendar(dateString)}T${time}`
}

// Google Calendar URL
const googleCalendarUrl = computed(() => {
  const startDate = formatDateForCalendar(props.startDate)
  const endDate = formatDateForCalendar(props.startDate)
  const params = new URLSearchParams({
    action: 'TEMPLATE',
    text: props.title,
    dates: `${startDate}/${endDate}`,
    details: props.description,
    location: props.location
  })
  return `https://calendar.google.com/calendar/render?${params.toString()}`
})

// Office 365 Calendar URL
const office365CalendarUrl = computed(() => {
  const startDate = new Date(`${props.startDate}T09:00:00`).toISOString()
  const endDate = new Date(`${props.startDate}T17:00:00`).toISOString()
  const params = new URLSearchParams({
    path: '/calendar/action/compose',
    rru: 'addevent',
    subject: props.title,
    body: props.description,
    startdt: startDate,
    enddt: endDate,
    location: props.location
  })
  return `https://outlook.office.com/calendar/0/deeplink/compose?${params.toString()}`
})

// ICS file download
const icsDownloadUrl = computed(() => {
  const startDate = formatDateTimeForICS(props.startDate, '090000')
  const endDate = formatDateTimeForICS(props.startDate, '170000')

  const icsContent = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//FED Workshop//EN',
    'BEGIN:VEVENT',
    `UID:${Date.now()}@fedworkshop.com`,
    `DTSTAMP:${formatDateTimeForICS(new Date().toISOString().split('T')[0])}`,
    `DTSTART:${startDate}`,
    `DTEND:${endDate}`,
    `SUMMARY:${props.title}`,
    `DESCRIPTION:${props.description.replace(/\n/g, '\\n')}`,
    `LOCATION:${props.location}`,
    'END:VEVENT',
    'END:VCALENDAR'
  ].join('\r\n')

  const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' })
  return URL.createObjectURL(blob)
})
</script>

<style scoped>
.btn-calendar {
  font-size: 0.875rem;
  padding: 0.5rem 0.75rem;
  background: var(--color-dev-dark);
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: opacity 0.2s;
  white-space: nowrap;
}

.btn-calendar:hover {
  opacity: 0.9;
}

.btn-calendar-option {
  padding: 0.75rem 1rem;
  background: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  text-align: center;
  font-weight: 500;
}

.btn-calendar-option:hover {
  background: var(--color-dev-dark);
  color: white;
  border-color: var(--color-dev-dark);
}
</style>
