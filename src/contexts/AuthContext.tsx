import React, { createContext, useContext, useState, useEffect } from 'react';

// Define user type
interface User {
  id: string;
  name: string;
}

// Define context type
interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  login: (username: string, password: string) => Promise<boolean>;
  logout: () => void;
}

// Create context with default values
const AuthContext = createContext<AuthContextType>({
  user: null,
  isAuthenticated: false,
  login: async () => false,
  logout: () => {},
});

// Sample user data (in a real app, this would come from a database)
const SAMPLE_USERS = [
  { id: '1', name: 'John Doe', username: 'john', password: 'password123' },
  { id: '2', name: 'Jane Smith', username: 'jane', password: 'password123' },
];

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    // Check for saved user on initial load
    const savedUser = localStorage.getItem('workshopUser');
    if (savedUser) {
      setUser(JSON.parse(savedUser));
    }
  }, []);

  // Login function
  const login = async (username: string, password: string): Promise<boolean> => {
    // Simulate API call delay
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Find user with matching credentials
    const foundUser = SAMPLE_USERS.find(
      u => u.username === username && u.password === password
    );
    
    if (foundUser) {
      const userObj = { id: foundUser.id, name: foundUser.name };
      setUser(userObj);
      localStorage.setItem('workshopUser', JSON.stringify(userObj));
      return true;
    }
    
    return false;
  };

  // Logout function
  const logout = () => {
    setUser(null);
    localStorage.removeItem('workshopUser');
  };

  return (
    <AuthContext.Provider value={{ 
      user, 
      isAuthenticated: !!user, 
      login, 
      logout 
    }}>
      {children}
    </AuthContext.Provider>
  );
};

// Custom hook for using the auth context
export const useAuth = () => useContext(AuthContext);