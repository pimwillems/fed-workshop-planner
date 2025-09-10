<template>
  <div class="container" style="padding: 2rem 1rem;">
    <div style="text-align: center; margin-bottom: 3rem;">
      <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-primary);">
        FED Workshop Schedule
      </h1>
      <p style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
        Browse upcoming FED workshops across different subjects. All workshops are planned by day to give teachers flexibility during their teaching time.
      </p>
    </div>

    <!-- View Toggle and Filters -->
    <div style="margin-bottom: 2rem;">
      <!-- View Toggle Buttons -->
      <div style="display: flex; justify-content: center; margin-bottom: 2rem;">
        <div style="display: inline-flex; background: var(--bg-secondary); border-radius: 0.5rem; padding: 0.25rem; gap: 0.25rem;">
          <button 
            @click="currentView = 'tiles'"
            :class="['btn', currentView === 'tiles' ? 'btn-primary' : '']"
            style="border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.875rem;"
          >
            📋 Workshop Tiles
          </button>
          <button 
            @click="currentView = 'calendar'"
            :class="['btn', currentView === 'calendar' ? 'btn-primary' : '']"
            style="border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.875rem;"
          >
            📅 Calendar View
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; align-items: center;">
        <div class="form-group" style="margin: 0; min-width: 200px;">
          <select 
            v-model="selectedSubject" 
            class="form-select"
            @change="applyFilters"
          >
            <option value="">All Subjects</option>
            <option value="Dev">Development</option>
            <option value="UX">UX Design</option>
            <option value="PO">Professional Skills</option>
            <option value="Research">Research</option>
            <option value="Portfolio">Portfolio</option>
            <option value="Misc">Miscellaneous</option>
          </select>
        </div>
        
        <div class="form-group" style="margin: 0; min-width: 200px;">
          <input 
            v-model="selectedDate" 
            type="date" 
            class="form-input"
            @change="applyFilters"
          />
        </div>
        
        <button @click="clearFilters" class="btn">
          Clear Filters
        </button>
      </div>
    </div>

    <div v-if="workshopsStore.loading" style="text-align: center; padding: 3rem;">
      <p style="color: var(--text-secondary);">Loading workshops...</p>
    </div>

    <div v-else-if="workshopsStore.error" style="text-align: center; padding: 3rem;">
      <p style="color: var(--color-portfolio-dark);">{{ workshopsStore.error }}</p>
      <button @click="loadWorkshops" class="btn btn-primary" style="margin-top: 1rem;">
        Try Again
      </button>
    </div>

    <div v-else>
      <div v-if="filteredWorkshops.length === 0" style="text-align: center; padding: 3rem;">
        <p style="color: var(--text-secondary); font-size: 1.1rem;">
          No workshops found matching your criteria.
        </p>
        <button @click="clearFilters" class="btn btn-primary" style="margin-top: 1rem;">
          View All Workshops
        </button>
      </div>

      <!-- Workshop Tiles View -->
      <div v-else-if="currentView === 'tiles'">
        <div class="grid grid-2">
          <div 
            v-for="workshop in filteredWorkshops" 
            :key="workshop.id" 
            class="card"
            :class="`subject-${workshop.subject.toLowerCase()}`"
          >
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
              <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
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
                <span 
                  v-if="getWorkshopWeekTag(new Date(workshop.date))"
                  :style="{
                    display: 'inline-block',
                    padding: '0.25rem 0.5rem',
                    borderRadius: '0.375rem',
                    fontSize: '0.7rem',
                    fontWeight: '500',
                    backgroundColor: 'var(--bg-secondary)',
                    color: 'var(--text-secondary)',
                    border: '1px solid var(--border-color)'
                  }"
                >
                  {{ getWorkshopWeekTag(new Date(workshop.date)) }}
                </span>
              </div>
              <div style="text-align: right; color: var(--text-muted); font-size: 0.875rem; font-weight: 600;">
                📅 {{ formatDate(workshop.date) }}
              </div>
            </div>

            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-primary);">
              {{ sanitizeText(workshop.title) }}
            </h3>

            <p style="color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5;">
              {{ sanitizeText(workshop.description) }}
            </p>

            <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 0.875rem;">
              <span>
                👨‍🏫 {{ sanitizeText(workshop.teacher.name) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Calendar View -->
      <div v-else-if="currentView === 'calendar'">
        <div style="background: var(--bg-secondary); border-radius: 0.75rem; padding: 1.5rem; overflow-x: auto;">
          <!-- Calendar Header -->
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <button @click="changeMonth(-1)" class="btn" style="padding: 0.5rem;">
              ← Previous
            </button>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">
              {{ currentMonthYear }}
            </h3>
            <button @click="changeMonth(1)" class="btn" style="padding: 0.5rem;">
              Next →
            </button>
          </div>

          <!-- Days of week header -->
          <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; margin-bottom: 1px;">
            <div 
              v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" 
              :key="day"
              style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);"
            >
              {{ day }}
            </div>
          </div>

          <!-- Calendar Grid -->
          <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px;">
            <div 
              v-for="day in calendarDays" 
              :key="`${day.date}-${day.isCurrentMonth}`"
              :style="{
                background: day.isCurrentMonth ? 'var(--bg-primary)' : 'var(--bg-muted)',
                minHeight: '120px',
                padding: '0.5rem',
                border: '1px solid var(--border-color)',
                opacity: day.isCurrentMonth ? 1 : 0.5
              }"
            >
              <!-- Day number and lesson week info -->
              <div style="margin-bottom: 0.5rem;">
                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                  {{ day.dayNumber }}
                </div>
                <div v-if="day.lessonWeekInfo.displayText" style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500;">
                  {{ day.lessonWeekInfo.displayText }}
                </div>
                <div v-if="!day.lessonWeekInfo.isTeaching" 
                     :style="{
                       fontSize: '0.65rem',
                       fontWeight: '600',
                       color: day.lessonWeekInfo.status === 'holiday' ? '#dc2626' : '#ea580c',
                       backgroundColor: day.lessonWeekInfo.status === 'holiday' ? '#fef2f2' : '#fff7ed',
                       padding: '0.125rem 0.25rem',
                       borderRadius: '0.25rem',
                       marginTop: '0.25rem',
                       textTransform: 'uppercase',
                       letterSpacing: '0.025em'
                     }">
                  Geen aanbod
                </div>
              </div>

              <!-- Workshops for this day -->
              <div v-for="workshop in day.workshops" :key="workshop.id" style="margin-bottom: 0.25rem;">
                <div 
                  :style="{
                    backgroundColor: getSubjectColor(workshop.subject),
                    color: getSubjectTextColor(workshop.subject),
                    padding: '0.25rem 0.5rem',
                    borderRadius: '0.25rem',
                    fontSize: '0.75rem',
                    fontWeight: '500',
                    lineHeight: '1.2',
                    cursor: 'pointer'
                  }"
                  :title="`${sanitizeText(workshop.title)} by ${sanitizeText(workshop.teacher.name)}`"
                >
                  {{ sanitizeText(workshop.title) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Subject } from '~/types'
import { useWorkshopsStore } from '~/store/workshops'
import { sanitizeText } from '~/utils/sanitization'
import { getLessonWeekInfo, getWorkshopWeekTag } from '~/utils/lessonWeeks'

const workshopsStore = useWorkshopsStore()

const selectedSubject = ref('')
const selectedDate = ref('')
const currentView = ref('tiles') // 'tiles' or 'calendar'
const currentCalendarMonth = ref(new Date())

const filteredWorkshops = computed(() => workshopsStore.filteredWorkshops)

const currentMonthYear = computed(() => {
  return currentCalendarMonth.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric'
  })
})

const calendarDays = computed(() => {
  const year = currentCalendarMonth.value.getFullYear()
  const month = currentCalendarMonth.value.getMonth()
  
  // Get first day of the month
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  
  // Get the day of week for the first day (0 = Sunday)
  const startDayOfWeek = firstDay.getDay()
  
  // Get days to show (including previous/next month days)
  const days = []
  
  // Add previous month days
  const prevMonth = new Date(year, month - 1, 0)
  for (let i = startDayOfWeek - 1; i >= 0; i--) {
    const date = new Date(year, month - 1, prevMonth.getDate() - i)
    const lessonWeekInfo = getLessonWeekInfo(date)
    days.push({
      date: date.toISOString().split('T')[0],
      dayNumber: date.getDate(),
      isCurrentMonth: false,
      workshops: [],
      lessonWeekInfo
    })
  }
  
  // Add current month days
  for (let day = 1; day <= lastDay.getDate(); day++) {
    const date = new Date(year, month, day)
    const dateString = date.toISOString().split('T')[0]
    const dayWorkshops = filteredWorkshops.value.filter(w => w.date === dateString)
    const lessonWeekInfo = getLessonWeekInfo(date)
    
    days.push({
      date: dateString,
      dayNumber: day,
      isCurrentMonth: true,
      workshops: dayWorkshops,
      lessonWeekInfo
    })
  }
  
  // Add next month days to complete the grid (42 days = 6 weeks)
  const remainingDays = 42 - days.length
  for (let day = 1; day <= remainingDays; day++) {
    const date = new Date(year, month + 1, day)
    const lessonWeekInfo = getLessonWeekInfo(date)
    days.push({
      date: date.toISOString().split('T')[0],
      dayNumber: day,
      isCurrentMonth: false,
      workshops: [],
      lessonWeekInfo
    })
  }
  
  return days
})

const applyFilters = () => {
  workshopsStore.setFilters({
    subject: selectedSubject.value as Subject || undefined,
    date: selectedDate.value || undefined
  })
}

const clearFilters = () => {
  selectedSubject.value = ''
  selectedDate.value = ''
  workshopsStore.clearFilters()
}

const loadWorkshops = async () => {
  await workshopsStore.fetchWorkshops()
}

const changeMonth = (direction: number) => {
  const newDate = new Date(currentCalendarMonth.value)
  newDate.setMonth(newDate.getMonth() + direction)
  currentCalendarMonth.value = newDate
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
  await loadWorkshops()
})

// SEO
useSeoMeta({
  title: 'FED Workshop Schedule | FED Learning Hub',
  description: 'Browse upcoming FED workshops across different subjects including Development, UX Design, Product Owner, Research, and Portfolio.'
})
</script>