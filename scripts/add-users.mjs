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

  // Teacher accounts - UPDATE THESE WITH YOUR ACTUAL USERS
  const teachers = [
    { email: 'm.putman@fontys.nl', name: 'Maikel Putman' },
    { email: 's.vanoers@fontys.nl', name: 'Stan van Oers' },
    { email: 'g.segers@fontys.nl', name: 'Guido Segers' },
    { email: 'a.eyck@fontys.nl', name: 'Anke Eyck' },
    { email: 'l.derkx@fontys.nl', name: 'Luuk Derkx' }
  ]

  // Admin accounts - UPDATE THESE WITH YOUR ACTUAL ADMINS
  const admins = [
    { email: 'p.willems@fontys.nl', name: 'Pim Willems' },
    { email: 'd.schol@fontys.nl', name: 'David Schol' }
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