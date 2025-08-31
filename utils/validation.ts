export interface ValidationResult {
  valid: boolean
  message?: string
}

export const validateEmail = (email: string): ValidationResult => {
  if (!email) {
    return { valid: false, message: 'Email is required' }
  }

  if (email.length > 254) {
    return { valid: false, message: 'Email must be less than 255 characters' }
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(email)) {
    return { valid: false, message: 'Invalid email format' }
  }

  return { valid: true }
}

export const validateString = (value: string, fieldName: string, minLength: number = 1, maxLength: number = 255): ValidationResult => {
  if (!value || typeof value !== 'string') {
    return { valid: false, message: `${fieldName} is required` }
  }

  if (value.trim().length < minLength) {
    return { valid: false, message: `${fieldName} must be at least ${minLength} characters long` }
  }

  if (value.length > maxLength) {
    return { valid: false, message: `${fieldName} must be less than ${maxLength} characters` }
  }

  return { valid: true }
}

export const validatePassword = (password: string): ValidationResult => {
  if (!password) {
    return { valid: false, message: 'Password is required' }
  }

  if (password.length < 8) {
    return { valid: false, message: 'Password must be at least 8 characters long' }
  }

  if (password.length > 128) {
    return { valid: false, message: 'Password must be less than 128 characters' }
  }

  if (!/(?=.*[a-z])/.test(password)) {
    return { valid: false, message: 'Password must contain at least one lowercase letter' }
  }

  if (!/(?=.*[A-Z])/.test(password)) {
    return { valid: false, message: 'Password must contain at least one uppercase letter' }
  }

  if (!/(?=.*\d)/.test(password)) {
    return { valid: false, message: 'Password must contain at least one number' }
  }

  return { valid: true }
}

export const validateDate = (dateString: string): ValidationResult => {
  if (!dateString) {
    return { valid: false, message: 'Date is required' }
  }

  const dateRegex = /^\d{4}-\d{2}-\d{2}$/
  if (!dateRegex.test(dateString)) {
    return { valid: false, message: 'Date must be in YYYY-MM-DD format' }
  }

  const date = new Date(dateString)
  if (isNaN(date.getTime())) {
    return { valid: false, message: 'Invalid date' }
  }

  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  if (date < today) {
    return { valid: false, message: 'Date cannot be in the past' }
  }

  const oneYearFromNow = new Date()
  oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() + 1)
  
  if (date > oneYearFromNow) {
    return { valid: false, message: 'Date cannot be more than one year in the future' }
  }

  return { valid: true }
}

export const validateSubject = (subject: string): ValidationResult => {
  const validSubjects = ['dev', 'ux', 'po', 'research', 'portfolio', 'misc']
  
  if (!subject) {
    return { valid: false, message: 'Subject is required' }
  }

  if (!validSubjects.includes(subject.toLowerCase())) {
    return { valid: false, message: 'Invalid subject' }
  }

  return { valid: true }
}