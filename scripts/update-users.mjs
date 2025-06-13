import { PrismaClient } from '@prisma/client'
import bcrypt from 'bcryptjs'

const prisma = new PrismaClient()

async function main() {
  console.log('Updating user accounts with correct details...')

  const defaultPassword = process.env.DEFAULT_USER_PASSWORD || 'changeme123'
  const hashedPassword = await bcrypt.hash(defaultPassword, 12)

  // User updates mapping old email to new details
  const userUpdates = [
    // Teachers
    {
      oldEmail: 'pvujicic@howest.be',
      newEmail: 'petra.vujicic@fontys.nl',
      name: 'Petra Vujicic',
      role: 'TEACHER'
    },
    {
      oldEmail: 'svanoers@howest.be', 
      newEmail: 's.vanoers@fontys.nl',
      name: 'Stan van Oers',
      role: 'TEACHER'
    },
    {
      oldEmail: 'gsegers@howest.be',
      newEmail: 'g.segers@fontys.nl', 
      name: 'Guido Segers',
      role: 'TEACHER'
    },
    {
      oldEmail: 'aeyck@howest.be',
      newEmail: 'a.eyck@fontys.nl',
      name: 'Anke Eyck', 
      role: 'TEACHER'
    },
    {
      oldEmail: 'lderkx@howest.be',
      newEmail: 'l.derkx@fontys.nl',
      name: 'Luuk Derkx',
      role: 'TEACHER'
    },
    // Admins
    {
      oldEmail: 'dschol@howest.be',
      newEmail: 'd.schol@fontys.nl',
      name: 'David Schol',
      role: 'ADMIN'
    },
    {
      oldEmail: 'pwillems@howest.be', 
      newEmail: 'p.willems@fontys.nl',
      name: 'Pim Willems',
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