import { PrismaClient } from '@prisma/client'

// Global variable to store the Prisma client instance
// This prevents multiple instances in development due to hot reloading
declare global {
  var __prisma: PrismaClient | undefined
}

let prisma: PrismaClient

if (process.env.NODE_ENV === 'production') {
  prisma = new PrismaClient()
} else {
  if (!global.__prisma) {
    global.__prisma = new PrismaClient()
  }
  prisma = global.__prisma
}

export { prisma }

// Helper function to transform database enums to frontend types
export const transformUser = (user: any) => ({
  ...user,
  role: user.role.toLowerCase()
})

export const transformWorkshop = (workshop: any) => ({
  ...workshop,
  subject: transformSubjectFromDb(workshop.subject),
  teacher: workshop.teacher ? transformUser(workshop.teacher) : undefined
})

export const transformSubjectToDb = (subject: string): string => {
  const mapping: Record<string, string> = {
    'Dev': 'DEV',
    'UX': 'UX',
    'PO': 'PO',
    'Research': 'RESEARCH',
    'Portfolio': 'PORTFOLIO',
    'Misc': 'MISC'
  }
  return mapping[subject] || 'MISC'
}

export const transformSubjectFromDb = (subject: string): string => {
  const mapping: Record<string, string> = {
    'DEV': 'Dev',
    'UX': 'UX',
    'PO': 'PO',
    'RESEARCH': 'Research',
    'PORTFOLIO': 'Portfolio',
    'MISC': 'Misc'
  }
  return mapping[subject] || 'Misc'
}

export const transformRoleToDb = (role: string): string => {
  return role.toUpperCase()
}

export const transformRoleFromDb = (role: string): string => {
  return role.toLowerCase()
}