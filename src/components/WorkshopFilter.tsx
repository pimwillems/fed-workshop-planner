import React, { useState } from 'react';
import { Filter } from 'lucide-react';
import { LEARNING_OUTCOMES } from '../types/Workshop';

interface WorkshopFilterProps {
  subjects: string[];
  learningOutcomes: string[];
  onFilterChange: (filters: { subjects: string[]; learningOutcomes: string[] }) => void;
}

const WorkshopFilter: React.FC<WorkshopFilterProps> = ({
  subjects,
  learningOutcomes,
  onFilterChange,
}) => {
  const [selectedSubjects, setSelectedSubjects] = useState<string[]>([]);
  const [selectedOutcomes, setSelectedOutcomes] = useState<string[]>([]);
  const [isFilterOpen, setIsFilterOpen] = useState(false);

  const toggleSubject = (subject: string) => {
    setSelectedSubjects(prev => {
      const newSelection = prev.includes(subject)
        ? prev.filter(s => s !== subject)
        : [...prev, subject];
      
      onFilterChange({
        subjects: newSelection,
        learningOutcomes: selectedOutcomes,
      });
      
      return newSelection;
    });
  };

  const toggleOutcome = (outcome: string) => {
    setSelectedOutcomes(prev => {
      const newSelection = prev.includes(outcome)
        ? prev.filter(o => o !== outcome)
        : [...prev, outcome];
      
      onFilterChange({
        subjects: selectedSubjects,
        learningOutcomes: newSelection,
      });
      
      return newSelection;
    });
  };

  const clearFilters = () => {
    setSelectedSubjects([]);
    setSelectedOutcomes([]);
    onFilterChange({ subjects: [], learningOutcomes: [] });
  };

  return (
    <div className="mb-6">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">Filters</h2>
        <button
          onClick={() => setIsFilterOpen(!isFilterOpen)}
          className="md:hidden flex items-center space-x-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100"
        >
          <Filter className="h-4 w-4" />
          <span>Filter</span>
        </button>
      </div>
      
      <div className={`mt-4 ${isFilterOpen ? 'block' : 'hidden md:block'}`}>
        <div className="grid md:grid-cols-2 gap-6">
          <div>
            <h3 className="font-medium mb-2 text-gray-700">Subjects</h3>
            <div className="space-y-1.5">
              {subjects.map((subject) => (
                <label
                  key={subject}
                  className="flex items-center space-x-2 cursor-pointer"
                >
                  <input
                    type="checkbox"
                    checked={selectedSubjects.includes(subject)}
                    onChange={() => toggleSubject(subject)}
                    className="rounded text-blue-600 focus:ring-blue-500 h-4 w-4"
                  />
                  <span>{subject}</span>
                </label>
              ))}
            </div>
          </div>
          
          <div>
            <h3 className="font-medium mb-2 text-gray-700">Learning Outcomes</h3>
            <div className="space-y-2">
              {LEARNING_OUTCOMES.map((outcome) => (
                <label
                  key={outcome.code}
                  className="flex items-start space-x-2 cursor-pointer"
                >
                  <input
                    type="checkbox"
                    checked={selectedOutcomes.includes(outcome.code)}
                    onChange={() => toggleOutcome(outcome.code)}
                    className="rounded text-blue-600 focus:ring-blue-500 h-4 w-4 mt-1"
                  />
                  <div>
                    <span className="font-medium">{outcome.code}</span>
                    <span className="text-sm text-gray-600 block">
                      {outcome.title}
                    </span>
                  </div>
                </label>
              ))}
            </div>
          </div>
        </div>
        
        {(selectedSubjects.length > 0 || selectedOutcomes.length > 0) && (
          <div className="mt-4 flex justify-end">
            <button
              onClick={clearFilters}
              className="px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded-md transition"
            >
              Clear All Filters
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default WorkshopFilter