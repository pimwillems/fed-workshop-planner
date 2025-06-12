import jwt from 'jsonwebtoken'
import type { CreateWorkshopData } from '~/types'
import { prisma, transformWorkshop, transformSubjectToDb } from '~/utils/db'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  
  try {
    // Verify authentication
    const authHeader = getHeader(event, 'authorization')
    
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      throw createError({
        statusCode: 401,
        statusMessage: 'Authorization token required'
      })
    }

    const token = authHeader.substring(7)
    const decoded = jwt.verify(token, config.jwtSecret) as any
    
    const user = await prisma.user.findUnique({
      where: { id: decoded.userId }
    })
    if (!user) {
      throw createError({
        statusCode: 401,
        statusMessage: 'User not found'
      })
    }

    // Check if user is teacher or admin
    if (user.role !== 'TEACHER' && user.role !== 'ADMIN') {
      throw createError({
        statusCode: 403,
        statusMessage: 'Only teachers can create workshops'
      })
    }

    const body = await readBody<CreateWorkshopData>(event)
    
    // Validate required fields
    if (!body.title || !body.description || !body.subject || !body.date) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Title, description, subject, and date are required'
      })
    }

    // Validate subject
    const validSubjects = ['Dev', 'UX', 'PO', 'Research', 'Portfolio', 'Misc']
    if (!validSubjects.includes(body.subject)) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Invalid subject'
      })
    }

    // Validate date format (YYYY-MM-DD)
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/
    if (!dateRegex.test(body.date)) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Date must be in YYYY-MM-DD format'
      })
    }

    // Create new workshop in database
    const newWorkshop = await prisma.workshop.create({
      data: {
        title: body.title,
        description: body.description,
        subject: transformSubjectToDb(body.subject),
        date: body.date,
        teacherId: user.id
      },
      include: {
        teacher: {
          select: {
            id: true,
            email: true,
            name: true,
            role: true,
            createdAt: true,
            updatedAt: true
          }
        }
      }
    })

    // Transform workshop for frontend
    const transformedWorkshop = transformWorkshop(newWorkshop)

    return {
      success: true,
      data: {
        workshop: transformedWorkshop
      }
    }
  } catch (error: any) {
    console.error('Error creating workshop:', error)
    
    if (error.statusCode) {
      throw error
    }
    
    if (error.name === 'JsonWebTokenError' || error.name === 'TokenExpiredError') {
      throw createError({
        statusCode: 401,
        statusMessage: 'Invalid or expired token'
      })
    }
    
    throw createError({
      statusCode: 500,
      statusMessage: 'Failed to create workshop'
    })
  }
})