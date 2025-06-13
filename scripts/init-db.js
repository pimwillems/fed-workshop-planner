#!/usr/bin/env node

import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

async function initDatabase() {
  try {
    console.log('🚀 Initializing database...')
    
    // Test connection
    await prisma.$connect()
    console.log('✅ Database connection successful')
    
    // Check if tables exist
    try {
      await prisma.user.count()
      console.log('✅ Tables already exist')
    } catch (error) {
      console.log('❌ Tables do not exist, this is expected for first deployment')
      console.log('Database schema will be created by Prisma during deployment')
    }
    
  } catch (error) {
    console.error('❌ Database initialization failed:', error)
    process.exit(1)
  } finally {
    await prisma.$disconnect()
  }
}

initDatabase()