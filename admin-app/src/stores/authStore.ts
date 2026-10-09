import { create } from 'zustand';
import api from '@/services/api';

interface User {
  id: number;
  username: string;
  email: string;
  capabilities: string[];
}

interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  loading: boolean;
  error: string | null;
  verifyAuth: () => Promise<void>;
  logout: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  isAuthenticated: false,
  loading: true,
  error: null,
  verifyAuth: async () => {
    try {
      set({ loading: true });
      const response = await api.get('/wp/v2/users/me');
      set({
        user: response.data,
        isAuthenticated: true,
        loading: false,
      });
    } catch (error: any) {
      set({
        user: null,
        isAuthenticated: false,
        error: error.message || 'Authentication failed',
        loading: false,
      });
    }
  },
  logout: () => {
    set({
      user: null,
      isAuthenticated: false,
      error: null,
    });
  },
}));
