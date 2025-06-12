import bcrypt from 'bcryptjs'
import jwt from 'jsonwebtoken'
import type { RegisterData } from '~/types'
import { prisma, transformUser } from '~/utils/db'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  
  try {
    const body = await readBody<RegisterData>(event)
    
    if (!body.email || !body.password || !body.name) {
      throw createError({
        statusCode: 400,
        statusMessage: 'Name, email and password are required'
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