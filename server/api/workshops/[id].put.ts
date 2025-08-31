import jwt from 'jsonwebtoken'
import type { UpdateWorkshopData } from '~/types'
import { prisma, transformWorkshop, transformSubjectToDb } from '~/utils/db'
import { validateString, validateSubject, validateDate } from '~/utils/validation'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  
  try {
    const workshopId = getRouterParam(event, 'id')
    
    if (!workshopId) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Workshop ID is required'
      })
    }

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

    // Find workshop
    const workshop = await prisma.workshop.findUnique({
      where: { id: workshopId },
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
    
    if (!workshop) {
      throw createError({
        statusCode: 404,
        statusMessage: 'Workshop not found'
      })
    }

    // Check authorization (only the teacher who created it or admin can update)
    if (workshop.teacherId !== user.id && user.role !== 'ADMIN') {
      throw createError({
        statusCode: 403,
        statusMessage: 'You can only update your own workshops'
      })
    }

    const body = await readBody<Partial<UpdateWorkshopData>>(event)
    
    // Validate title if provided
    if (body.title !== undefined) {
      const titleValidation = validateString(body.title, 'Title', 1, 200)
      if (!titleValidation.valid) {
        throw createError({
          statusCode: 400,
          statusMessage: titleValidation.message
        })
      }
    }

    // Validate description if provided
    if (body.description !== undefined) {
      const descriptionValidation = validateString(body.description, 'Description', 1, 1000)
      if (!descriptionValidation.valid) {
        throw createError({
          statusCode: 400,
          statusMessage: descriptionValidation.message
        })
      }
    }

    // Validate subject if provided
    if (body.subject) {
      const subjectValidation = validateSubject(body.subject)
      if (!subjectValidation.valid) {
        throw createError({
          statusCode: 400,
          statusMessage: subjectValidation.message
        })
      }
    }

    // Validate date if provided
    if (body.date) {
      const dateValidation = validateDate(body.date)
      if (!dateValidation.valid) {
        throw createError({
          statusCode: 400,
          statusMessage: dateValidation.message
        })
      }
    }

    // Build update data
    const updateData: any = {}
    if (body.title) updateData.title = body.title
    if (body.description) updateData.description = body.description
    if (body.subject) updateData.subject = transformSubjectToDb(body.subject)
    if (body.date) updateData.date = body.date

    // Update workshop in database
    const updatedWorkshop = await prisma.workshop.update({
      where: { id: workshopId },
      data: updateData,
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
    const transformedWorkshop = transformWorkshop(updatedWorkshop)

    return {
      success: true,
      data: {
        workshop: transformedWorkshop
      }
    }
  } catch (error: any) {
    console.error('Error updating workshop:', error)
    
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
      statusMessage: 'Failed to update workshop'
    })
  }
})