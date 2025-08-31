import jwt from 'jsonwebtoken'
import type { CreateWorkshopData } from '~/types'
import { prisma, transformWorkshop, transformSubjectToDb } from '~/utils/db'
import { validateString, validateSubject, validateDate } from '~/utils/validation'

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
    
    // Validate input fields
    const titleValidation = validateString(body.title, 'Title', 1, 200)
    if (!titleValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: titleValidation.message
      })
    }

    const descriptionValidation = validateString(body.description, 'Description', 1, 1000)
    if (!descriptionValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: descriptionValidation.message
      })
    }

    const subjectValidation = validateSubject(body.subject)
    if (!subjectValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: subjectValidation.message
      })
    }

    const dateValidation = validateDate(body.date)
    if (!dateValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: dateValidation.message
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