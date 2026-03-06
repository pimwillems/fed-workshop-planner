import jwt from 'jsonwebtoken'
import { prisma, transformUser } from '~/utils/db'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()

  try {
    // Get and verify JWT token
    const token = getCookie(event, 'auth-token') || getHeader(event, 'authorization')?.replace('Bearer ', '')

    if (!token) {
      throw createError({
        statusCode: 401,
        statusMessage: 'Authentication required'
      })
    }

    let decoded
    try {
      decoded = jwt.verify(token, config.jwtSecret) as any
    } catch (error) {
      throw createError({
        statusCode: 401,
        statusMessage: 'Invalid token'
      })
    }

    // Find user in database
    const user = await prisma.user.findUnique({
      where: { id: decoded.userId }
    })

    if (!user) {
      throw createError({
        statusCode: 404,
        statusMessage: 'User not found'
      })
    }

    // Check if user is admin
    if (user.role !== 'ADMIN') {
      throw createError({
        statusCode: 403,
        statusMessage: 'Admin access required'
      })
    }

    // Get all users (without passwords)
    const users = await prisma.user.findMany({
      orderBy: {
        name: 'asc'
      }
    })

    // Transform users (remove passwords)
    const transformedUsers = users.map(user => {
      const { password, ...userWithoutPassword } = user
      return transformUser(userWithoutPassword)
    })

    return {
      success: true,
      data: {
        users: transformedUsers
      }
    }
  } catch (error: any) {
    console.error('Get users error:', error)

    if (error.statusCode) {
      throw error
    }

    throw createError({
      statusCode: 500,
      statusMessage: 'Internal server error'
    })
  }
})
