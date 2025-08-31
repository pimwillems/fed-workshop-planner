import DOMPurify from 'dompurify'

export const sanitizeHtml = (html: string): string => {
  if (typeof html !== 'string') {
    return ''
  }
  
  return DOMPurify.sanitize(html, {
    ALLOWED_TAGS: ['b', 'i', 'em', 'strong', 'br'],
    ALLOWED_ATTR: [],
    KEEP_CONTENT: true,
    RETURN_DOM: false,
    RETURN_DOM_FRAGMENT: false,
    RETURN_DOM_IMPORT: false
  })
}

export const stripHtml = (html: string): string => {
  if (typeof html !== 'string') {
    return ''
  }
  
  return DOMPurify.sanitize(html, {
    ALLOWED_TAGS: [],
    ALLOWED_ATTR: [],
    KEEP_CONTENT: true
  })
}

export const sanitizeText = (text: string): string => {
  if (typeof text !== 'string') {
    return ''
  }
  
  return text
    .replace(/[<>]/g, '') // Remove angle brackets
    .replace(/javascript:/gi, '') // Remove javascript: protocol
    .replace(/data:/gi, '') // Remove data: protocol
    .trim()
}