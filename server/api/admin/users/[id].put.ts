import jwt from 'jsonwebtoken'
import { prisma, transformUser } from '~/utils/db'
import { validateEmail, validateString } from '~/utils/validation'

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

    const targetUser = await prisma.user.findUnique({ where: { id: userId } })
    if (!targetUser) throw createError({ statusCode: 404, statusMessage: 'User not found' })

    const body = await readBody(event)
    const { name, email, role } = body

    const nameValidation = validateString(name, 'Name', 2)
    if (!nameValidation.valid) throw createError({ statusCode: 400, statusMessage: nameValidation.message })

    const emailValidation = validateEmail(email)
    if (!emailValidation.valid) throw createError({ statusCode: 400, statusMessage: emailValidation.message })

    const validRoles = ['TEACHER', 'ADMIN']
    const dbRole = role?.toUpperCase()
    if (!validRoles.includes(dbRole)) throw createError({ statusCode: 400, statusMessage: 'Invalid role' })

    // Check email uniqueness if changed
    const normalizedEmail = email.toLowerCase().trim()
    if (normalizedEmail !== targetUser.email) {
      const existing = await prisma.user.findUnique({ where: { email: normalizedEmail } })
      if (existing) throw createError({ statusCode: 409, statusMessage: 'A user with this email already exists' })
    }

    const updated = await prisma.user.update({
      where: { id: userId },
      data: { name: name.trim(), email: normalizedEmail, role: dbRole }
    })

    const { password: _, ...userWithoutPassword } = updated

    return {
      success: true,
      message: `User ${updated.name} updated successfully`,
      data: { user: transformUser(userWithoutPassword) }
    }
  } catch (error: any) {
    console.error('Update user error:', error)
    if (error.statusCode) throw error
    throw createError({ statusCode: 500, statusMessage: 'Internal server error' })
  }
})
