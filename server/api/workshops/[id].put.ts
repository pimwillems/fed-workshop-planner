import jwt from 'jsonwebtoken'
import type { UpdateWorkshopData } from '~/types'
import { prisma, transformWorkshop, transformSubjectToDb } from '~/utils/db'

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
    
    // Validate subject if provided
    if (body.subject) {
      const validSubjects = ['Dev', 'UX', 'PO', 'Research', 'Portfolio', 'Misc']
      if (!validSubjects.includes(body.subject)) {
        throw createError({
          statusCode: 400,
          statusMessage: 'Invalid subject'
        })
      }
    }

    // Validate date format if provided
    if (body.date) {
      const dateRegex = /^\d{4}-\d{2}-\d{2}$/
      if (!dateRegex.test(body.date)) {
        throw createError({
          statusCode: 400,
          statusMessage: 'Date must be in YYYY-MM-DD format'
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