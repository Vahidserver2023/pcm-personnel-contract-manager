import { create } from 'zustand';
import api from '@/services/api';

interface Calculation {
  id: number;
  employee_id: number;
  calculation_month: string;
  total_gross: number;
  total_deductions: number;
  net_pay: number;
  status: string;
}

interface PayrollState {
  calculations: Calculation[];
  loading: boolean;
  error: string | null;
  fetchCalculations: (year: string, month: string) => Promise<void>;
  createCalculation: (data: any) => Promise<number>;
  updateCalculationStatus: (id: number, status: string) => Promise<void>;
}

export const usePayrollStore = create<PayrollState>((set) => ({
  calculations: [],
  loading: false,
  error: null,
  fetchCalculations: async (year: string, month: string) => {
    try {
      set({ loading: true });
      const response = await api.get(`/pcm/v1/salary-calculations`, {
        params: { year, month },
      });
      set({ calculations: response.data.data, loading: false });
    } catch (error: any) {
      set({
        error: error.message || 'Failed to fetch calculations',
        loading: false,
      });
    }
  },
  createCalculation: async (data: any) => {
    try {
      const response = await api.post(`/pcm/v1/salary-calculations`, data);
      return response.data.id;
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to create calculation');
    }
  },
  updateCalculationStatus: async (id: number, status: string) => {
    try {
      await api.put(`/pcm/v1/salary-calculations/${id}/status`, { status });
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to update calculation');
    }
  },
}));
