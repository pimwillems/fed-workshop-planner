import { PrismaClient } from '@prisma/client'
import bcrypt from 'bcryptjs'

const prisma = new PrismaClient()

async function main() {
  console.log('Updating user accounts with correct details...')

  const defaultPassword = process.env.DEFAULT_USER_PASSWORD
  if (!defaultPassword) {
    console.error('❌ ERROR: DEFAULT_USER_PASSWORD environment variable is required!')
    console.error('Usage: DEFAULT_USER_PASSWORD="your_password" node scripts/update-users.mjs')
    process.exit(1)
  }
  const hashedPassword = await bcrypt.hash(defaultPassword, 12)

  // User updates mapping old email to new details - UPDATE WITH YOUR ACTUAL USERS
  const userUpdates = [
    // Teachers - Example entries, update with your actual users
    {
      oldEmail: 'old.email@domain.com',
      newEmail: 'new.email@fontys.nl',
      name: 'Teacher Name',
      role: 'TEACHER'
    },
    // Admins - Example entries, update with your actual admins
    {
      oldEmail: 'old.admin@domain.com',
      newEmail: 'new.admin@fontys.nl',
      name: 'Admin Name',
      role: 'ADMIN'
    }
  ]

  for (const update of userUpdates) {
    try {
      // First, try to find the user by old email
      const existingUser = await prisma.user.findUnique({
        where: { email: update.oldEmail }
      })

      if (existingUser) {
        // Update the existing user
        const updatedUser = await prisma.user.update({
          where: { email: update.oldEmail },
          data: {
            email: update.newEmail,
            name: update.name,
            role: update.role
          }
        })
        console.log(`✓ Updated: ${updatedUser.name} (${update.oldEmail} → ${updatedUser.email})`)
      } else {
        // Create new user if old one doesn't exist
        const newUser = await prisma.user.create({
          data: {
            email: update.newEmail,
            name: update.name,
            password: hashedPassword,
            role: update.role
          }
        })
        console.log(`✓ Created: ${newUser.name} (${newUser.email})`)
      }
    } catch (error) {
      console.log(`✗ Failed to update ${update.name}:`, error.message)
    }
  }

  console.log('\\n📧 All accounts updated with correct details!')
  console.log('🔑 Password set from environment variable')
}

main()
  .catch((e) => {
    console.error('Error updating users:', e)
    process.exit(1)
  })
  .finally(async () => {
    await prisma.$disconnect()
  })