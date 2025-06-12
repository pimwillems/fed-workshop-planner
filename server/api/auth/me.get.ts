import jwt from 'jsonwebtoken'
import { prisma, transformUser } from '~/utils/db'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  
  try {
    const authHeader = getHeader(event, 'authorization')
    
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      throw createError({
        statusCode: 401,
        statusMessage: 'Authorization token required'
      })
    }

    const token = authHeader.substring(7)
    
    // Verify JWT token
    const decoded = jwt.verify(token, config.jwtSecret) as any
    
    // Find user in database
    const user = await prisma.user.findUnique({
      where: { id: decoded.userId }
    })
    if (!user) {
      throw createError({
        statusCode: 401,
        statusMessage: 'User not found'
      })
    }

    // Remove password from response and transform user
    const { password, ...userWithoutPassword } = user
    const transformedUser = transformUser(userWithoutPassword)

    return {
      success: true,
      data: {
        user: transformedUser
      }
    }
  } catch (error: any) {
    console.error('Auth verification error:', error)
    
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
      statusMessage: 'Internal server error'
    })
  }
})