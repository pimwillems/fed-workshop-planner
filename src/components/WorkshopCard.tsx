import React from 'react';
import { Workshop, LEARNING_OUTCOMES, SUBJECTS } from '../types/Workshop';

interface WorkshopCardProps {
  workshop: Workshop;
}

const WorkshopCard: React.FC<WorkshopCardProps> = ({ workshop }) => {
  const getSubjectColor = (subject: string) => {
    const [mainSubject] = subject.split(' ');
    const colors: Record<string, string> = {
      'DEV': 'bg-blue-100 text-blue-800 border-blue-300',
      'UX': 'bg-purple-100 text-purple-800 border-purple-300',
      'PORT': 'bg-green-100 text-green-800 border-green-300',
      'RES': 'bg-yellow-100 text-yellow-800 border-yellow-300',
      'PM': 'bg-pink-100 text-pink-800 border-pink-300',
    };

    return colors[mainSubject] || 'bg-gray-100 text-gray-800 border-gray-300';
  };

  const formatSubject = (subject: string) => {
    const [code, level] = subject.split(' ');
    const mainSubject = SUBJECTS.find(s => s.code === code);
    return mainSubject ? `${mainSubject.name} ${level}` : subject;
  };

  const formatDate = (timestamp: number) => {
    const date = new Date(timestamp);
    return date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    });
  };

  return (
    <div className="bg-white rounded-lg shadow-md overflow-hidden transform transition hover:scale-[1.02] hover:shadow-lg">
      <div className={`px-4 py-3 border-l-4 ${getSubjectColor(workshop.subject)}`}>
        <div className="flex justify-between items-start">
          <h3 className="text-lg font-semibold">{workshop.topic}</h3>
          <span className={`px-2 py-1 rounded text-xs font-medium ${getSubjectColor(workshop.subject)}`}>
            {formatSubject(workshop.subject)}
          </span>
        </div>
        <p className="text-gray-600 mt-2">{workshop.teacher}</p>
      </div>
      
      <div className="px-4 py-3 border-t border-gray-100">
        <div className="mb-3">
          <h4 className="text-sm font-semibold text-gray-500 uppercase mb-2">Learning Outcomes</h4>
          <div className="flex flex-wrap gap-2">
            {workshop.learningOutcomes.map((outcomeCode) => {
              const outcome = LEARNING_OUTCOMES.find(lo => lo.code === outcomeCode);
              return (
                <div key={outcomeCode} className="group relative">
                  <span className="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">
                    {outcomeCode}
                  </span>
                  <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none">
                    {outcome?.title}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
        
        <div className="text-xs text-gray-500 mt-2 text-right">
          Added: {formatDate(workshop.timestamp)}
        </div>
      </div>
    </div>
  );
};

export default WorkshopCard;