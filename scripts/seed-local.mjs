import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Starting local database seeding...');

  // Clean up existing data
  console.log('🧹 Cleaning up the database...');
  await prisma.workshop.deleteMany({});
  await prisma.user.deleteMany({});
  console.log('✅ Database cleaned successfully.');

  // Create admin user with email: admin, password: admin
  console.log('👤 Creating admin user...');
  const salt = await bcrypt.genSalt(12);
  const hashedPassword = await bcrypt.hash('admin', salt);

  const admin = await prisma.user.create({
    data: {
      email: 'admin',
      name: 'Admin User',
      password: hashedPassword,
      role: 'ADMIN',
    },
  });

  console.log(`✅ Admin user created: ${admin.email}`);

  // Create 3 workshops with different subjects in December 2025
  console.log('📚 Creating 3 workshops...');

  const workshops = [
    {
      title: 'Advanced React Patterns',
      description: 'Learn advanced patterns in React including custom hooks, compound components, and render props. This workshop will help you write more maintainable and scalable React applications.',
      subject: 'DEV',
      date: '2025-12-05',
      teacherId: admin.id,
    },
    {
      title: 'User Experience Design Fundamentals',
      description: 'Discover the principles of great UX design. We will cover user research, wireframing, prototyping, and usability testing to create user-centered designs.',
      subject: 'UX',
      date: '2025-12-12',
      teacherId: admin.id,
    },
    {
      title: 'Agile Project Management Workshop',
      description: 'Master the art of agile project management as a Product Owner. Learn about sprint planning, backlog refinement, stakeholder management, and delivering value iteratively.',
      subject: 'PO',
      date: '2025-12-19',
      teacherId: admin.id,
    },
  ];

  await prisma.workshop.createMany({
    data: workshops,
  });

  console.log('✅ 3 workshops created successfully.');
  console.log('🎉 Local seeding finished!');
  console.log('\n📋 Login credentials:');
  console.log('   Email: admin');
  console.log('   Password: admin');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
