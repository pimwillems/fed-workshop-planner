import React, { useState, useEffect } from 'react';
import { Workshop } from '../types/Workshop';
import { workshopService } from '../services/workshopService';
import WeekSelector from '../components/WeekSelector';
import WorkshopCard from '../components/WorkshopCard';
import WorkshopFilter from '../components/WorkshopFilter';
import { Filter as FilterAlt, Loader } from 'lucide-react';

const WorkshopSchedulePage: React.FC = () => {
  const [loading, setLoading] = useState(true);
  const [workshops, setWorkshops] = useState<Workshop[]>([]);
  const [currentWeek, setCurrentWeek] = useState(1);
  const [showFilters, setShowFilters] = useState(false);
  const [filters, setFilters] = useState({
    subjects: [] as string[],
    learningOutcomes: [] as string[],
  });

  useEffect(() => {
    const fetchWorkshops = async () => {
      setLoading(true);
      try {
        const data = await workshopService.getWorkshops();
        setWorkshops(data);
      } catch (error) {
        console.error('Failed to fetch workshops:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchWorkshops();
  }, []);

  // Get unique subjects and learning outcomes for filters
  const allSubjects = [...new Set(workshops.map(w => w.subject))];
  const allLearningOutcomes = [
    'LO1', 'LO2', 'LO3', 'LO4', 'LO5'
  ];

  // Filter workshops by current week and applied filters
  const filteredWorkshops = workshops.filter(workshop => {
    // Filter by week
    if (workshop.weekNumber !== currentWeek) {
      return false;
    }
    
    // Filter by subject if any subjects are selected
    if (filters.subjects.length > 0 && !filters.subjects.includes(workshop.subject)) {
      return false;
    }
    
    // Filter by learning outcomes if any are selected
    if (filters.learningOutcomes.length > 0) {
      // Check if workshop has at least one of the selected learning outcomes
      const hasSelectedOutcome = workshop.learningOutcomes.some(outcome => 
        filters.learningOutcomes.includes(outcome)
      );
      
      if (!hasSelectedOutcome) {
        return false;
      }
    }
    
    return true;
  });

  const handleWeekChange = (week: number) => {
    setCurrentWeek(week);
  };

  const handleFilterChange = (newFilters: { subjects: string[]; learningOutcomes: string[] }) => {
    setFilters(newFilters);
  };

  return (
    <div>
      <div className="mb-6">
        <h1 className="text-3xl font-bold text-center mb-2">Workshop Schedule</h1>
        <p className="text-gray-600 text-center">
          Browse and discover workshops scheduled for each week
        </p>
      </div>

      <WeekSelector 
        currentWeek={currentWeek} 
        totalWeeks={20} 
        onChange={handleWeekChange} 
      />

      <div className="md:hidden mb-4">
        <button
          onClick={() => setShowFilters(!showFilters)}
          className="w-full py-2 px-4 flex items-center justify-center space-x-2 bg-gray-100 hover:bg-gray-200 rounded-md transition"
        >
          <FilterAlt className="h-4 w-4" />
          <span>{showFilters ? 'Hide Filters' : 'Show Filters'}</span>
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div className={`lg:col-span-1 ${showFilters ? 'block' : 'hidden lg:block'}`}>
          <WorkshopFilter
            subjects={allSubjects}
            learningOutcomes={allLearningOutcomes}
            onFilterChange={handleFilterChange}
          />
        </div>
        
        <div className="lg:col-span-3">
          {loading ? (
            <div className="flex justify-center items-center h-64">
              <Loader className="h-8 w-8 text-blue-600 animate-spin" />
            </div>
          ) : filteredWorkshops.length === 0 ? (
            <div className="bg-yellow-50 border border-yellow-200 rounded-md p-4 text-center">
              <p className="text-yellow-700">No workshops found for week {currentWeek} with the selected filters.</p>
              <p className="text-sm text-yellow-600 mt-2">Try selecting a different week or adjusting your filters.</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {filteredWorkshops.map((workshop) => (
                <WorkshopCard key={workshop.id} workshop={workshop} />
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default WorkshopSchedulePage;