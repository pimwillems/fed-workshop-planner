import bcrypt from 'bcryptjs'
import jwt from 'jsonwebtoken'
import type { RegisterData } from '~/types'
import { prisma, transformUser } from '~/utils/db'
import { validateEmail, validateString, validatePassword } from '~/utils/validation'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  
  try {
    const body = await readBody<RegisterData>(event)
    
    // Validate input fields
    const emailValidation = validateEmail(body.email)
    if (!emailValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: emailValidation.message
      })
    }

    const nameValidation = validateString(body.name, 'Name', 1, 100)
    if (!nameValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: nameValidation.message
      })
    }

    const passwordValidation = validatePassword(body.password)
    if (!passwordValidation.valid) {
      throw createError({
        statusCode: 400,
        statusMessage: passwordValidation.message
      })
    }

    // Check if user already exists
    const existingUser = await prisma.user.findUnique({
      where: { email: body.email }
    })
    if (existingUser) {
      throw createError({
        statusCode: 409,
        statusMessage: 'User with this email already exists'
      })
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(body.password, 12)

    // Create new user in database
    const newUser = await prisma.user.create({
      data: {
        email: body.email,
        name: body.name,
        password: hashedPassword,
        role: 'TEACHER' // All new registrations are teachers by default
      }
    })

    // Generate JWT token
    const token = jwt.sign(
      { 
        userId: newUser.id, 
        email: newUser.email, 
        role: newUser.role 
      },
      config.jwtSecret,
      { expiresIn: '7d' }
    )

    // Remove password from response and transform user
    const { password, ...userWithoutPassword } = newUser
    const transformedUser = transformUser(userWithoutPassword)

    return {
      success: true,
      data: {
        user: transformedUser,
        token
      }
    }
  } catch (error: any) {
    console.error('Registration error:', error)
    
    if (error.statusCode) {
      throw error
    }
    
    throw createError({
      statusCode: 500,
      statusMessage: 'Internal server error'
    })
  }
})