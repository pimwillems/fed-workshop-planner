import { PrismaClient } from '@prisma/client'
import bcrypt from 'bcryptjs'

const prisma = new PrismaClient()

async function main() {
  const email = process.argv[2]
  const testPassword = process.argv[3]
  
  if (!email) {
    console.error('Usage: node scripts/debug-user.mjs <email> [test-password]')
    process.exit(1)
  }

  console.log(`Looking up user: ${email}`)

  try {
    const user = await prisma.user.findUnique({
      where: { email }
    })
    
    if (!user) {
      console.log('❌ User not found')
      return
    }
    
    console.log('✓ User found:')
    console.log(`  ID: ${user.id}`)
    console.log(`  Name: ${user.name}`)
    console.log(`  Email: ${user.email}`)
    console.log(`  Role: ${user.role}`)
    console.log(`  Password hash: ${user.password.substring(0, 20)}...`)
    
    if (testPassword) {
      console.log(`\nTesting password: "${testPassword}"`)
      const isValid = await bcrypt.compare(testPassword, user.password)
      console.log(`Password valid: ${isValid ? '✓' : '❌'}`)
    }
    
  } catch (error) {
    console.error(`✗ Error: ${error.message}`)
  }
}

main()
  .catch((e) => {
    console.error('Error:', e)
    process.exit(1)
  })
  .finally(async () => {
    await prisma.$disconnect()
  })