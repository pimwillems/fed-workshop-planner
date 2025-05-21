import { Workshop } from '../types/Workshop';

// Mock data for demonstration
const MOCK_WORKSHOPS: Workshop[] = [
  {
    id: '1',
    weekNumber: 1,
    subject: 'Mathematics',
    topic: 'Introduction to Algebra',
    teacher: 'John Doe',
    learningOutcomes: ['LO1', 'LO3'],
    timestamp: Date.now() - 1000000,
  },
  {
    id: '2',
    weekNumber: 1,
    subject: 'Science',
    topic: 'Physics Fundamentals',
    teacher: 'Jane Smith',
    learningOutcomes: ['LO2', 'LO4', 'LO5'],
    timestamp: Date.now() - 2000000,
  },
  {
    id: '3',
    weekNumber: 2,
    subject: 'Language Arts',
    topic: 'Essay Writing Techniques',
    teacher: 'Alice Johnson',
    learningOutcomes: ['LO1', 'LO2'],
    timestamp: Date.now() - 3000000,
  },
  {
    id: '4',
    weekNumber: 2,
    subject: 'Social Studies',
    topic: 'World War II Analysis',
    teacher: 'Robert Brown',
    learningOutcomes: ['LO3', 'LO5'],
    timestamp: Date.now() - 4000000,
  },
  {
    id: '5',
    weekNumber: 3,
    subject: 'Art',
    topic: 'Impressionist Painting Techniques',
    teacher: 'Emma Wilson',
    learningOutcomes: ['LO2', 'LO4'],
    timestamp: Date.now() - 5000000,
  },
  {
    id: '6',
    weekNumber: 3,
    subject: 'Music',
    topic: 'Music Theory Fundamentals',
    teacher: 'Michael Davis',
    learningOutcomes: ['LO1', 'LO3', 'LO4'],
    timestamp: Date.now() - 6000000,
  },
  {
    id: '7',
    weekNumber: 4,
    subject: 'Physical Education',
    topic: 'Team Building Activities',
    teacher: 'Sarah Thompson',
    learningOutcomes: ['LO2', 'LO5'],
    timestamp: Date.now() - 7000000,
  },
  {
    id: '8',
    weekNumber: 4,
    subject: 'Technology',
    topic: 'Introduction to Programming',
    teacher: 'David Anderson',
    learningOutcomes: ['LO1', 'LO3', 'LO5'],
    timestamp: Date.now() - 8000000,
  },
];

// In a real application, this would be replaced with API calls
class WorkshopService {
  private workshops: Workshop[] = [...MOCK_WORKSHOPS];

  // Get all workshops
  async getWorkshops(): Promise<Workshop[]> {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 800));
    return [...this.workshops];
  }

  // Get workshops by week
  async getWorkshopsByWeek(weekNumber: number): Promise<Workshop[]> {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 500));
    return this.workshops.filter(w => w.weekNumber === weekNumber);
  }

  // Add a new workshop
  async addWorkshop(workshopData: Omit<Workshop, 'id'>): Promise<Workshop> {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    const newWorkshop: Workshop = {
      ...workshopData,
      id: Date.now().toString(), // Generate a simple ID
    };
    
    this.workshops.push(newWorkshop);
    return newWorkshop;
  }

  // Delete a workshop (for future expansion)
  async deleteWorkshop(id: string): Promise<boolean> {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 600));
    
    const initialLength = this.workshops.length;
    this.workshops = this.workshops.filter(w => w.id !== id);
    
    return this.workshops.length < initialLength;
  }
}

export const workshopService = new WorkshopService();