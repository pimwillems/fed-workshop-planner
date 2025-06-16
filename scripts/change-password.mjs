import { PrismaClient } from '@prisma/client'
import bcrypt from 'bcryptjs'

const prisma = new PrismaClient()

async function main() {
  const email = process.argv[2]
  const newPassword = process.argv[3]
  
  if (!email || !newPassword) {
    console.error('Usage: node scripts/change-password.mjs <email> <new-password>')
    process.exit(1)
  }

  console.log(`Changing password for ${email}...`)

  const hashedPassword = await bcrypt.hash(newPassword, 12)

  try {
    const updatedUser = await prisma.user.update({
      where: { email },
      data: { password: hashedPassword }
    })
    
    console.log(`✓ Password updated for ${updatedUser.name} (${updatedUser.email})`)
  } catch (error) {
    console.error(`✗ Failed to update password: ${error.message}`)
    process.exit(1)
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