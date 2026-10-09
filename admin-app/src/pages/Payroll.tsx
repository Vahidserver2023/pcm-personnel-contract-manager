import { useEffect, useState } from 'react';
import { usePayrollStore } from '@/stores/payrollStore';

function Payroll() {
  const { calculations, loading, fetchCalculations } = usePayrollStore();
  const [year, setYear] = useState(new Date().getFullYear().toString());
  const [month, setMonth] = useState(String(new Date().getMonth() + 1).padStart(2, '0'));

  useEffect(() => {
    fetchCalculations(year, month);
  }, [year, month, fetchCalculations]);

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold text-gray-900 mb-6">حقوق‌ها</h1>

      <div className="flex gap-4 mb-6">
        <input
          type="number"
          value={year}
          onChange={(e) => setYear(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
          placeholder="سال"
        />
        <input
          type="number"
          min="1"
          max="12"
          value={month}
          onChange={(e) => setMonth(String(parseInt(e.target.value)).padStart(2, '0'))}
          className="px-4 py-2 border border-gray-300 rounded-lg"
          placeholder="ماه"
        />
      </div>

      {loading ? (
        <div className="text-center">بارگذاری...</div>
      ) : (
        <div className="bg-white rounded-lg shadow-sm p-6">
          <p className="text-gray-500">تعداد محاسبات: {calculations.length}</p>
        </div>
      )}
    </div>
  );
}

export default Payroll;
