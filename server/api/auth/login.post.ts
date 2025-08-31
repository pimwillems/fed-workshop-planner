import bcrypt from 'bcryptjs'
import jwt from 'jsonwebtoken'
import type { LoginCredentials } from '~/types'
import { prisma, transformUser } from '~/utils/db'
import { validateEmail } from '~/utils/validation'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  
  try {
    const body = await readBody<LoginCredentials>(event)
    
    // Validate email format
    const emailValidation = validateEmail(body.email)
    if (!emailValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: emailValidation.message
      })
    }

    if (!body.password) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Password is required'
      })
    }

    // Find user in database
    const user = await prisma.user.findUnique({
      where: { email: body.email }
    })
    
    if (!user) {
      throw createError({
        statusCode: 401,
        statusMessage: 'Invalid credentials'
      })
    }

    // Check password
    const isValidPassword = await bcrypt.compare(body.password, user.password)
    if (!isValidPassword) {
      throw createError({
        statusCode: 401,
        statusMessage: 'Invalid credentials'
      })
    }

    // Generate JWT token
    const token = jwt.sign(
      { 
        userId: user.id, 
        email: user.email, 
        role: user.role 
      },
      config.jwtSecret,
      { expiresIn: '7d' }
    )

    // Remove password from response and transform user
    const { password, ...userWithoutPassword } = user
    const transformedUser = transformUser(userWithoutPassword)

    return {
      success: true,
      data: {
        user: transformedUser,
        token
      }
    }
  } catch (error: any) {
    console.error('Login error:', error)
    
    if (error.statusCode) {
      throw error
    }
    
    throw createError({
      statusCode: 500,
      statusMessage: 'Internal server error'
    })
  }
})