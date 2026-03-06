import jwt from 'jsonwebtoken'
import bcrypt from 'bcryptjs'
import { prisma } from '~/utils/db'
import { validatePassword } from '~/utils/validation'

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

    // Find admin user in database
    const adminUser = await prisma.user.findUnique({
      where: { id: decoded.userId }
    })

    if (!adminUser) {
      throw createError({
        statusCode: 404,
        statusMessage: 'Admin user not found'
      })
    }

    // Check if user is admin
    if (adminUser.role !== 'ADMIN') {
      throw createError({
        statusCode: 403,
        statusMessage: 'Admin access required'
      })
    }

    // Get user ID from route params
    const userId = getRouterParam(event, 'id')
    if (!userId) {
      throw createError({
        statusCode: 400,
        statusMessage: 'User ID is required'
      })
    }

    // Get new password from request body
    const body = await readBody(event)
    const { newPassword } = body

    // Validate password
    const passwordValidation = validatePassword(newPassword)
    if (!passwordValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: passwordValidation.message
      })
    }

    // Find target user
    const targetUser = await prisma.user.findUnique({
      where: { id: userId }
    })

    if (!targetUser) {
      throw createError({
        statusCode: 404,
        statusMessage: 'Target user not found'
      })
    }

    // Hash new password
    const hashedPassword = await bcrypt.hash(newPassword, 12)

    // Update password in database
    await prisma.user.update({
      where: { id: userId },
      data: { password: hashedPassword }
    })

    return {
      success: true,
      message: `Password reset successfully for ${targetUser.name}`
    }
  } catch (error: any) {
    console.error('Reset password error:', error)

    if (error.statusCode) {
      throw error
    }

    throw createError({
      statusCode: 500,
      statusMessage: 'Internal server error'
    })
  }
})
