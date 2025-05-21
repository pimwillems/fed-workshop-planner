export interface Workshop {
  id: string;
  weekNumber: number;
  subject: string;
  topic: string;
  teacher: string;
  learningOutcomes: string[];
  timestamp: number;
}

export interface LearningOutcome {
  code: string;
  title: string;
}

export const LEARNING_OUTCOMES: LearningOutcome[] = [
  { code: 'LO1', title: 'User Interaction' },
  { code: 'LO2', title: 'Web development' },
  { code: 'LO3', title: 'Iterative process' },
  { code: 'LO4', title: 'Professional Standard' },
  { code: 'LO5', title: 'Personal Leadership' },
];

export interface Subject {
  code: string;
  name: string;
  levels: string[];
}

export const SUBJECTS: Subject[] = [
  { code: 'DEV', name: 'Development', levels: ['201', '202', '203'] },
  { code: 'UX', name: 'User Experience', levels: ['201', '202', '203'] },
  { code: 'PORT', name: 'Portfolio', levels: ['201', '202', '203'] },
  { code: 'RES', name: 'Research', levels: ['201', '202', '203'] },
  { code: 'PM', name: 'Project Management', levels: ['201', '202', '203'] },
];