import { PrismaClient } from '@prisma/client';
import { faker } from '@faker-js/faker';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

/**
 * -----------------------------------------------------------------
 *  MAIN SEED FUNCTION
 * -----------------------------------------------------------------
 * @description This function is the entry point for seeding the database.
 *              It first cleans up existing data and then proceeds to
 *              create a new user and a series of workshops.
 * -----------------------------------------------------------------
 */
async function main() {
  console.log('🌱 Starting the seeding process...');

  // ---------------------------------------------------------------
  //  Clean up the database to ensure a fresh start
  // ---------------------------------------------------------------
  console.log('🧹 Cleaning up the database...');
  await prisma.workshop.deleteMany({});
  await prisma.user.deleteMany({});
  console.log('✅ Database cleaned successfully.');

  // ---------------------------------------------------------------
  //  Create a single user with the 'TEACHER' role
  // ---------------------------------------------------------------
  console.log("👤 Creating a 'TEACHER' user...");

  const salt = await bcrypt.genSalt(10);
  const hashedPassword = await bcrypt.hash('password123', salt);

  const teacher = await prisma.user.create({
    data: {
      email: 'teacher@example.com',
      name: 'Dr. Evelyn Reed',
      password: hashedPassword,
      role: 'TEACHER',
    },
  });

  console.log(`✅ User '${teacher.name}' created successfully.`);

  // ---------------------------------------------------------------
  //  Define a list of realistic Front End Development workshop titles
  // ---------------------------------------------------------------
  const workshopTitles = [
    'Advanced React Patterns',
    'Vue.js for Beginners',
    'Mastering TypeScript',
    'Next.js: From Development to Production',
    'State Management with Redux and Zustand',
    'Modern CSS with Flexbox and Grid',
    'Building Responsive Web Apps',
    'Introduction to SvelteKit',
    'Testing JavaScript with Jest and Cypress',
    'GraphQL for Front End Developers',
    'Web Performance Optimization',
    'Three.js: Creating 3D Web Experiences',
    'Building a Design System with Storybook',
    'CI/CD for Front End Projects',
    'Node.js and Express for the Front End Developer',
    'Web Accessibility (A11Y) Workshop',
    'Animating the Web with GSAP',
    'D3.js for Data Visualization',
    'Microfrontends: The Future of Web Development?',
    'Understanding Webpack and Vite',
  ];

  // ---------------------------------------------------------------
  //  Generate and create 20 random workshops
  // ---------------------------------------------------------------
  console.log('📚 Generating 20 Front End Development workshops...');

  const workshops = [];

  const oneMonthAgo = new Date();
  oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);

  const oneMonthInFuture = new Date();
  oneMonthInFuture.setMonth(oneMonthInFuture.getMonth() + 1);
  
  for (let i = 0; i < 20; i++) {
    const randomDate = faker.date.between({
        from: oneMonthAgo,
        to: oneMonthInFuture
    });
    const formattedDate = `${randomDate.getFullYear()}-${String(
      randomDate.getMonth() + 1,
    ).padStart(2, '0')}-${String(randomDate.getDate()).padStart(2, '0')}`;

    workshops.push({
      title: workshopTitles[i],
      description: faker.lorem.paragraphs(3),
      subject: 'DEV',
      date: formattedDate,
      teacherId: teacher.id,
    });
  }

  await prisma.workshop.createMany({
    data: workshops,
  });

  console.log('✅ 20 workshops created successfully.');
  console.log('🎉 Seeding finished successfully!');
}

// -----------------------------------------------------------------
//  EXECUTION AND ERROR HANDLING
// -----------------------------------------------------------------
main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });