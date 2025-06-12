import jwt from 'jsonwebtoken'
import { prisma } from '~/utils/db'

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
      where: { id: workshopId }
    })
    
    if (!workshop) {
      throw createError({
        statusCode: 404,
        statusMessage: 'Workshop not found'
      })
    }

    // Check authorization (only the teacher who created it or admin can delete)
    if (workshop.teacherId !== user.id && user.role !== 'ADMIN') {
      throw createError({
        statusCode: 403,
        statusMessage: 'You can only delete your own workshops'
      })
    }

    // Delete workshop from database
    await prisma.workshop.delete({
      where: { id: workshopId }
    })

    return {
      success: true,
      data: {
        message: 'Workshop deleted successfully'
      }
    }
  } catch (error: any) {
    console.error('Error deleting workshop:', error)
    
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
      statusMessage: 'Failed to delete workshop'
    })
  }
})