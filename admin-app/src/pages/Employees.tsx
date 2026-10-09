import { useEffect, useState } from 'react';
import api from '@/services/api';

interface Employee {
  id: number;
  employee_code: string;
  first_name: string;
  last_name: string;
  email: string;
  mobile: string;
  department_id: number;
  position_id: number;
  status: string;
}

function Employees() {
  const [employees, setEmployees] = useState<Employee[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('active');

  useEffect(() => {
    const fetchEmployees = async () => {
      try {
        setLoading(true);
        const response = await api.get('/pcm/v1/employees', {
          params: { status: filter },
        });
        setEmployees(response.data.data || []);
      } catch (error) {
        console.error('Failed to fetch employees:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchEmployees();
  }, [filter]);

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-gray-900">کارمندان</h1>
        <button className="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
          افزودن کارمند جدید
        </button>
      </div>

      <div className="bg-white rounded-lg shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-200">
          <div className="flex gap-4">
            {['active', 'inactive', 'all'].map((status) => (
              <button
                key={status}
                onClick={() => setFilter(status)}
                className={`px-4 py-2 rounded-lg transition-colors ${
                  filter === status
                    ? 'bg-primary text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                }`}
              >
                {status === 'active' ? 'فعال' : status === 'inactive' ? 'غیرفعال' : 'همه'}
              </button>
            ))}
          </div>
        </div>

        {loading ? (
          <div className="p-6 text-center text-gray-500">بارگذاری...</div>
        ) : employees.length === 0 ? (
          <div className="p-6 text-center text-gray-500">کارمندی یافت نشد</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th className="px-6 py-3 text-right text-sm font-medium text-gray-700">کد</th>
                  <th className="px-6 py-3 text-right text-sm font-medium text-gray-700">نام</th>
                  <th className="px-6 py-3 text-right text-sm font-medium text-gray-700">ایمیل</th>
                  <th className="px-6 py-3 text-right text-sm font-medium text-gray-700">موبایل</th>
                  <th className="px-6 py-3 text-right text-sm font-medium text-gray-700">وضعیت</th>
                  <th className="px-6 py-3 text-right text-sm font-medium text-gray-700">عملیات</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {employees.map((emp) => (
                  <tr key={emp.id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 text-sm text-gray-900">{emp.employee_code}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">
                      {emp.first_name} {emp.last_name}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-600">{emp.email}</td>
                    <td className="px-6 py-4 text-sm text-gray-600">{emp.mobile}</td>
                    <td className="px-6 py-4 text-sm">
                      <span
                        className={`px-3 py-1 rounded-full text-xs font-medium ${
                          emp.status === 'active'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                        }`}
                      >
                        {emp.status === 'active' ? 'فعال' : 'غیرفعال'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-center">
                      <button className="text-blue-600 hover:underline">ویرایش</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}

export default Employees;
