import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Workshop, LEARNING_OUTCOMES, SUBJECTS } from '../types/Workshop';
import { workshopService } from '../services/workshopService';
import { useAuth } from '../contexts/AuthContext';
import { AlertCircle, Check } from 'lucide-react';

const SubmitWorkshopPage: React.FC = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    weekNumber: 1,
    mainSubject: '',
    subjectLevel: '',
    topic: '',
    teacherName: user?.name || '',
    learningOutcomes: [] as string[],
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => {
      // Reset subjectLevel when mainSubject changes
      if (name === 'mainSubject') {
        return { ...prev, [name]: value, subjectLevel: '' };
      }
      return { ...prev, [name]: value };
    });
    
    if (errors[name]) {
      setErrors(prev => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    }
  };

  const handleLearningOutcomeChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { value, checked } = e.target;
    
    setFormData(prev => {
      if (checked) {
        return { ...prev, learningOutcomes: [...prev.learningOutcomes, value] };
      } else {
        return { 
          ...prev, 
          learningOutcomes: prev.learningOutcomes.filter(outcome => outcome !== value)
        };
      }
    });
    
    if (errors.learningOutcomes) {
      setErrors(prev => {
        const newErrors = { ...prev };
        delete newErrors.learningOutcomes;
        return newErrors;
      });
    }
  };

  const validateForm = () => {
    const newErrors: Record<string, string> = {};
    
    if (!formData.mainSubject) {
      newErrors.mainSubject = 'Please select a subject';
    }
    
    if (!formData.subjectLevel) {
      newErrors.subjectLevel = 'Please select a level';
    }
    
    if (!formData.topic.trim()) {
      newErrors.topic = 'Please enter a topic';
    }
    
    if (!formData.teacherName.trim()) {
      newErrors.teacherName = 'Please enter teacher name';
    }
    
    if (formData.learningOutcomes.length === 0) {
      newErrors.learningOutcomes = 'Please select at least one learning outcome';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }
    
    setIsSubmitting(true);
    
    try {
      const subject = `${formData.mainSubject} ${formData.subjectLevel}`;
      const newWorkshop: Omit<Workshop, 'id'> = {
        weekNumber: formData.weekNumber,
        subject,
        topic: formData.topic,
        teacher: formData.teacherName,
        learningOutcomes: formData.learningOutcomes,
        timestamp: Date.now(),
      };
      
      await workshopService.addWorkshop(newWorkshop);
      setSubmitted(true);
      
      setTimeout(() => {
        setFormData({
          weekNumber: 1,
          mainSubject: '',
          subjectLevel: '',
          topic: '',
          teacherName: user?.name || '',
          learningOutcomes: [],
        });
        setSubmitted(false);
        navigate('/');
      }, 2000);
    } catch (error) {
      console.error('Failed to submit workshop:', error);
      setErrors({ form: 'Failed to submit workshop. Please try again.' });
    } finally {
      setIsSubmitting(false);
    }
  };

  const selectedSubject = SUBJECTS.find(s => s.code === formData.mainSubject);

  return (
    <div className="max-w-2xl mx-auto">
      <div className="text-center mb-8">
        <h1 className="text-3xl font-bold">Submit Workshop</h1>
        <p className="text-gray-600 mt-2">
          Use this form to submit a new workshop to the schedule
        </p>
      </div>
      
      {submitted ? (
        <div className="bg-green-50 border-l-4 border-green-500 p-4 mb-6 flex items-start">
          <Check className="h-6 w-6 text-green-500 mr-3 flex-shrink-0" />
          <div>
            <h3 className="text-green-800 font-medium">Workshop Submitted Successfully!</h3>
            <p className="text-green-700 mt-1">Your workshop has been added to the schedule.</p>
          </div>
        </div>
      ) : (
        <div className="bg-white shadow-md rounded-lg p-6">
          {errors.form && (
            <div className="mb-6 bg-red-50 border-l-4 border-red-500 p-4 text-red-700 flex items-start">
              <AlertCircle className="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" />
              <p>{errors.form}</p>
            </div>
          )}
          
          <form onSubmit={handleSubmit}>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label htmlFor="weekNumber" className="block text-gray-700 font-medium mb-2">
                  Week Number*
                </label>
                <select
                  id="weekNumber"
                  name="weekNumber"
                  value={formData.weekNumber}
                  onChange={handleInputChange}
                  className="w-full p-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  {Array.from({ length: 20 }, (_, i) => i + 1).map((week) => (
                    <option key={week} value={week}>
                      Week {week}
                    </option>
                  ))}
                </select>
              </div>
              
              <div>
                <label htmlFor="mainSubject" className="block text-gray-700 font-medium mb-2">
                  Subject*
                </label>
                <select
                  id="mainSubject"
                  name="mainSubject"
                  value={formData.mainSubject}
                  onChange={handleInputChange}
                  className={`w-full p-2.5 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                    errors.mainSubject ? 'border-red-500' : 'border-gray-300'
                  }`}
                >
                  <option value="">Select a subject</option>
                  {SUBJECTS.map((subject) => (
                    <option key={subject.code} value={subject.code}>
                      {subject.name}
                    </option>
                  ))}
                </select>
                {errors.mainSubject && (
                  <p className="mt-1 text-sm text-red-600">{errors.mainSubject}</p>
                )}
              </div>

              {formData.mainSubject && (
                <div className="md:col-span-2">
                  <label htmlFor="subjectLevel" className="block text-gray-700 font-medium mb-2">
                    Level*
                  </label>
                  <div className="flex gap-4">
                    {selectedSubject?.levels.map((level) => (
                      <label key={level} className="flex items-center">
                        <input
                          type="radio"
                          name="subjectLevel"
                          value={level}
                          checked={formData.subjectLevel === level}
                          onChange={handleInputChange}
                          className="mr-2 text-blue-600 focus:ring-blue-500"
                        />
                        {level}
                      </label>
                    ))}
                  </div>
                  {errors.subjectLevel && (
                    <p className="mt-1 text-sm text-red-600">{errors.subjectLevel}</p>
                  )}
                </div>
              )}
            </div>
            
            <div className="mt-6">
              <label htmlFor="topic" className="block text-gray-700 font-medium mb-2">
                Topic Description*
              </label>
              <textarea
                id="topic"
                name="topic"
                value={formData.topic}
                onChange={handleInputChange}
                rows={3}
                className={`w-full p-2.5 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                  errors.topic ? 'border-red-500' : 'border-gray-300'
                }`}
                placeholder="Describe the workshop topic..."
              />
              {errors.topic && (
                <p className="mt-1 text-sm text-red-600">{errors.topic}</p>
              )}
            </div>
            
            <div className="mt-6">
              <label htmlFor="teacherName" className="block text-gray-700 font-medium mb-2">
                Teacher Name*
              </label>
              <input
                id="teacherName"
                name="teacherName"
                type="text"
                value={formData.teacherName}
                onChange={handleInputChange}
                className={`w-full p-2.5 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                  errors.teacherName ? 'border-red-500' : 'border-gray-300'
                }`}
                placeholder="Enter teacher name"
              />
              {errors.teacherName && (
                <p className="mt-1 text-sm text-red-600">{errors.teacherName}</p>
              )}
            </div>
            
            <div className="mt-6">
              <label className="block text-gray-700 font-medium mb-2">
                Learning Outcomes*
              </label>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                {LEARNING_OUTCOMES.map((outcome) => (
                  <label key={outcome.code} className="flex items-start space-x-2 p-2 rounded-md hover:bg-gray-50">
                    <input
                      type="checkbox"
                      name="learningOutcomes"
                      value={outcome.code}
                      checked={formData.learningOutcomes.includes(outcome.code)}
                      onChange={handleLearningOutcomeChange}
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
              {errors.learningOutcomes && (
                <p className="mt-1 text-sm text-red-600">{errors.learningOutcomes}</p>
              )}
            </div>
            
            <div className="mt-8 flex justify-end space-x-4">
              <button
                type="button"
                onClick={() => navigate('/')}
                className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
              >
                Cancel
              </button>
              <button
                type="submit"
                disabled={isSubmitting}
                className={`px-6 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ${
                  isSubmitting ? 'opacity-70 cursor-not-allowed' : ''
                }`}
              >
                {isSubmitting ? 'Submitting...' : 'Submit Workshop'}
              </button>
            </div>
          </form>
        </div>
      )}
    </div>
  );
};

export default SubmitWorkshopPage;