import jwt from 'jsonwebtoken'
import { prisma } from '~/utils/db'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()

  try {
    const token = getCookie(event, 'auth-token') || getHeader(event, 'authorization')?.replace('Bearer ', '')

    if (!token) {
      throw createError({ statusCode: 401, statusMessage: 'Authentication required' })
    }

    let decoded
    try {
      decoded = jwt.verify(token, config.jwtSecret) as any
    } catch {
      throw createError({ statusCode: 401, statusMessage: 'Invalid token' })
    }

    const adminUser = await prisma.user.findUnique({ where: { id: decoded.userId } })
    if (!adminUser) throw createError({ statusCode: 404, statusMessage: 'Admin user not found' })
    if (adminUser.role !== 'ADMIN') throw createError({ statusCode: 403, statusMessage: 'Admin access required' })

    const userId = getRouterParam(event, 'id')
    if (!userId) throw createError({ statusCode: 400, statusMessage: 'User ID is required' })

    if (userId === decoded.userId) {
      throw createError({ statusCode: 400, statusMessage: 'You cannot delete your own account' })
    }

    const targetUser = await prisma.user.findUnique({ where: { id: userId } })
    if (!targetUser) throw createError({ statusCode: 404, statusMessage: 'User not found' })

    await prisma.user.delete({ where: { id: userId } })

    return {
      success: true,
      message: `User ${targetUser.name} deleted successfully`
    }
  } catch (error: any) {
    console.error('Delete user error:', error)
    if (error.statusCode) throw error
    throw createError({ statusCode: 500, statusMessage: 'Internal server error' })
  }
})
