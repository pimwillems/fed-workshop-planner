import React, { useState } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { MenuIcon, X, Calendar, LogIn, LogOut, Plus } from 'lucide-react';

const Navbar: React.FC = () => {
  const { isAuthenticated, user, logout } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const toggleMenu = () => setIsMenuOpen(!isMenuOpen);
  const closeMenu = () => setIsMenuOpen(false);

  const handleLogout = () => {
    logout();
    navigate('/');
    closeMenu();
  };

  return (
    <nav className="bg-blue-700 text-white shadow-md">
      <div className="container mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          {/* Logo and site name */}
          <Link 
            to="/" 
            className="flex items-center space-x-2 text-xl font-bold"
            onClick={closeMenu}
          >
            <Calendar className="h-6 w-6" />
            <span>Workshop Planner</span>
          </Link>

          {/* Desktop navigation */}
          <div className="hidden md:flex items-center space-x-4">
            <Link 
              to="/" 
              className={`px-3 py-2 rounded hover:bg-blue-600 transition ${
                location.pathname === '/' ? 'bg-blue-600' : ''
              }`}
            >
              Schedule
            </Link>
            
            {isAuthenticated ? (
              <>
                <Link 
                  to="/submit" 
                  className={`px-3 py-2 rounded hover:bg-blue-600 transition flex items-center space-x-1 ${
                    location.pathname === '/submit' ? 'bg-blue-600' : ''
                  }`}
                >
                  <Plus className="h-4 w-4" />
                  <span>Add Workshop</span>
                </Link>
                <div className="relative group">
                  <button className="px-3 py-2 rounded hover:bg-blue-600 transition">
                    {user?.name || 'Teacher'}
                  </button>
                  <div className="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                    <button
                      onClick={handleLogout}
                      className="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center"
                    >
                      <LogOut className="h-4 w-4 mr-2" />
                      <span>Logout</span>
                    </button>
                  </div>
                </div>
              </>
            ) : (
              <Link 
                to="/login" 
                className={`px-3 py-2 rounded hover:bg-blue-600 transition flex items-center space-x-1 ${
                  location.pathname === '/login' ? 'bg-blue-600' : ''
                }`}
              >
                <LogIn className="h-4 w-4" />
                <span>Teacher Login</span>
              </Link>
            )}
          </div>

          {/* Mobile menu button */}
          <button className="md:hidden" onClick={toggleMenu}>
            {isMenuOpen ? <X className="h-6 w-6" /> : <MenuIcon className="h-6 w-6" />}
          </button>
        </div>
      </div>

      {/* Mobile navigation */}
      {isMenuOpen && (
        <div className="md:hidden bg-blue-800 pb-4 px-2">
          <Link 
            to="/" 
            className={`block px-3 py-2 rounded my-1 hover:bg-blue-600 transition ${
              location.pathname === '/' ? 'bg-blue-600' : ''
            }`}
            onClick={closeMenu}
          >
            Schedule
          </Link>
          
          {isAuthenticated ? (
            <>
              <Link 
                to="/submit" 
                className={`block px-3 py-2 rounded my-1 hover:bg-blue-600 transition ${
                  location.pathname === '/submit' ? 'bg-blue-600' : ''
                }`}
                onClick={closeMenu}
              >
                <div className="flex items-center">
                  <Plus className="h-4 w-4 mr-2" />
                  <span>Add Workshop</span>
                </div>
              </Link>
              <div className="border-t border-blue-600 my-2"></div>
              <div className="px-3 py-2 text-blue-200">
                Signed in as: {user?.name || 'Teacher'}
              </div>
              <button
                onClick={handleLogout}
                className="w-full text-left px-3 py-2 rounded my-1 hover:bg-blue-600 transition"
              >
                <div className="flex items-center">
                  <LogOut className="h-4 w-4 mr-2" />
                  <span>Logout</span>
                </div>
              </button>
            </>
          ) : (
            <Link 
              to="/login" 
              className={`block px-3 py-2 rounded my-1 hover:bg-blue-600 transition ${
                location.pathname === '/login' ? 'bg-blue-600' : ''
              }`}
              onClick={closeMenu}
            >
              <div className="flex items-center">
                <LogIn className="h-4 w-4 mr-2" />
                <span>Teacher Login</span>
              </div>
            </Link>
          )}
        </div>
      )}
    </nav>
  );
};

export default Navbar;