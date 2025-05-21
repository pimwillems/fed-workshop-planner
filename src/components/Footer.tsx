import React from 'react';
import { Calendar } from 'lucide-react';

const Footer: React.FC = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-gray-800 text-white py-6">
      <div className="container mx-auto px-4">
        <div className="flex flex-col md:flex-row justify-between items-center">
          <div className="flex items-center mb-4 md:mb-0">
            <Calendar className="h-6 w-6 mr-2" />
            <span className="text-lg font-semibold">Workshop Planner</span>
          </div>
          
          <div className="text-sm text-gray-400">
            &copy; {currentYear} Workshop Planning System. All rights reserved.
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;