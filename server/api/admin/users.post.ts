import jwt from 'jsonwebtoken'
import bcrypt from 'bcryptjs'
import { prisma, transformUser } from '~/utils/db'
import { validateEmail, validateString, validatePassword } from '~/utils/validation'

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

    const body = await readBody(event)
    const { name, email, password, role } = body

    const nameValidation = validateString(name, 'Name', 2)
    if (!nameValidation.valid) throw createError({ statusCode: 400, statusMessage: nameValidation.message })

    const emailValidation = validateEmail(email)
    if (!emailValidation.valid) throw createError({ statusCode: 400, statusMessage: emailValidation.message })

    const passwordValidation = validatePassword(password)
    if (!passwordValidation.valid) throw createError({ statusCode: 400, statusMessage: passwordValidation.message })

    const validRoles = ['TEACHER', 'ADMIN']
    const dbRole = role?.toUpperCase()
    if (!validRoles.includes(dbRole)) throw createError({ statusCode: 400, statusMessage: 'Invalid role' })

    const existing = await prisma.user.findUnique({ where: { email: email.toLowerCase() } })
    if (existing) throw createError({ statusCode: 409, statusMessage: 'A user with this email already exists' })

    const hashedPassword = await bcrypt.hash(password, 12)

    const newUser = await prisma.user.create({
      data: {
        name: name.trim(),
        email: email.toLowerCase().trim(),
        password: hashedPassword,
        role: dbRole
      }
    })

    const { password: _, ...userWithoutPassword } = newUser

    return {
      success: true,
      message: `User ${newUser.name} created successfully`,
      data: { user: transformUser(userWithoutPassword) }
    }
  } catch (error: any) {
    console.error('Create user error:', error)
    if (error.statusCode) throw error
    throw createError({ statusCode: 500, statusMessage: 'Internal server error' })
  }
})
