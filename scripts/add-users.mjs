import { PrismaClient } from '@prisma/client'
import bcrypt from 'bcryptjs'

const prisma = new PrismaClient()

async function main() {
  console.log('Adding new user accounts...')

  // Get password from environment variable for security
  const defaultPassword = process.env.DEFAULT_USER_PASSWORD
  if (!defaultPassword) {
    console.error('❌ ERROR: DEFAULT_USER_PASSWORD environment variable is required!')
    console.error('Usage: DEFAULT_USER_PASSWORD="your_password" node scripts/add-users.mjs')
    process.exit(1)
  }
  const hashedPassword = await bcrypt.hash(defaultPassword, 12)

  // Teacher accounts
  const teachers = [
    { email: 'pvujicic@howest.be', name: 'Petar Vujicic' },
    { email: 'gsegers@howest.be', name: 'Gilles Segers' },
    { email: 'aeyck@howest.be', name: 'Arne Eyck' },
    { email: 'svanoers@howest.be', name: 'Stijn Van Oers' },
    { email: 'lderkx@howest.be', name: 'Lennert Derkx' }
  ]

  // Admin accounts
  const admins = [
    { email: 'pwillems@howest.be', name: 'Pim Willems' },
    { email: 'dschol@howest.be', name: 'Dries Schol' }
  ]

  console.log('Creating teacher accounts...')
  for (const teacher of teachers) {
    try {
      const user = await prisma.user.upsert({
        where: { email: teacher.email },
        update: {},
        create: {
          email: teacher.email,
          name: teacher.name,
          password: hashedPassword,
          role: 'TEACHER'
        }
      })
      console.log(`✓ Created teacher: ${user.name} (${user.email})`)
    } catch (error) {
      console.log(`✗ Failed to create teacher ${teacher.email}:`, error.message)
    }
  }

  console.log('Creating admin accounts...')
  for (const admin of admins) {
    try {
      const user = await prisma.user.upsert({
        where: { email: admin.email },
        update: {},
        create: {
          email: admin.email,
          name: admin.name,
          password: hashedPassword,
          role: 'ADMIN'
        }
      })
      console.log(`✓ Created admin: ${user.name} (${user.email})`)
    } catch (error) {
      console.log(`✗ Failed to create admin ${admin.email}:`, error.message)
    }
  }

  console.log('\\n📧 All accounts created with default password from environment variable')
  console.log('⚠️  Users should change their password on first login!')
}

main()
  .catch((e) => {
    console.error('Error creating users:', e)
    process.exit(1)
  })
  .finally(async () => {
    await prisma.$disconnect()
  })