import React from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface WeekSelectorProps {
  currentWeek: number;
  totalWeeks: number;
  onChange: (week: number) => void;
}

const WeekSelector: React.FC<WeekSelectorProps> = ({ 
  currentWeek, 
  totalWeeks, 
  onChange 
}) => {
  const handlePrevious = () => {
    if (currentWeek > 1) {
      onChange(currentWeek - 1);
    }
  };

  const handleNext = () => {
    if (currentWeek < totalWeeks) {
      onChange(currentWeek + 1);
    }
  };

  const weeks = Array.from({ length: totalWeeks }, (_, i) => i + 1);

  return (
    <div className="flex flex-col items-center mb-6">
      <h2 className="text-xl font-semibold mb-4">Select Week</h2>
      
      <div className="flex items-center">
        <button
          onClick={handlePrevious}
          disabled={currentWeek === 1}
          className="p-2 rounded-l bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
          aria-label="Previous week"
        >
          <ChevronLeft className="h-5 w-5" />
        </button>
        
        <div className="hidden md:flex">
          {weeks.map(week => (
            <button
              key={week}
              onClick={() => onChange(week)}
              className={`w-10 h-10 mx-0.5 flex items-center justify-center rounded-full transition-colors ${
                week === currentWeek
                  ? 'bg-blue-600 text-white font-semibold'
                  : 'hover:bg-gray-200'
              }`}
            >
              {week}
            </button>
          ))}
        </div>
        
        <select
          value={currentWeek}
          onChange={(e) => onChange(Number(e.target.value))}
          className="md:hidden h-10 mx-2 px-3 bg-white border border-gray-300 rounded"
          aria-label="Select week"
        >
          {weeks.map(week => (
            <option key={week} value={week}>
              Week {week}
            </option>
          ))}
        </select>
        
        <button
          onClick={handleNext}
          disabled={currentWeek === totalWeeks}
          className="p-2 rounded-r bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
          aria-label="Next week"
        >
          <ChevronRight className="h-5 w-5" />
        </button>
      </div>
      
      <div className="mt-4 text-center">
        <h3 className="text-2xl font-bold">Week {currentWeek}</h3>
      </div>
    </div>
  );
};

export default WeekSelector;