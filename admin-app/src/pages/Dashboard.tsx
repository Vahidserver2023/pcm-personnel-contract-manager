import { useEffect, useState } from 'react';
import api from '@/services/api';
import StatCard from '@/components/StatCard';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

interface DashboardStats {
  totalEmployees: number;
  activeContracts: number;
  pendingPayments: number;
  monthlyPayroll: number;
}

function Dashboard() {
  const [stats, setStats] = useState<DashboardStats>({
    totalEmployees: 0,
    activeContracts: 0,
    pendingPayments: 0,
    monthlyPayroll: 0,
  });
  const [chartData, setChartData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const empRes = await api.get('/pcm/v1/employees');
        const contractRes = await api.get('/pcm/v1/contracts?status=active');
        const paymentRes = await api.get('/pcm/v1/payments?status=pending');
        const payrollRes = await api.get('/pcm/v1/reports/payroll', {
          params: { year: new Date().getFullYear(), month: String(new Date().getMonth() + 1).padStart(2, '0') },
        });

        setStats({
          totalEmployees: empRes.data.data?.length || 0,
          activeContracts: contractRes.data.data?.length || 0,
          pendingPayments: paymentRes.data.data?.length || 0,
          monthlyPayroll: payrollRes.data.data?.totals?.total_gross || 0,
        });

        // Mock chart data - in reality this would come from the API
        setChartData([
          { month: 'فروردین', payroll: 1000000, payments: 950000 },
          { month: 'اردیبهشت', payroll: 1050000, payments: 1000000 },
          { month: 'خرداد', payroll: 1100000, payments: 1050000 },
          { month: 'تیر', payroll: 1050000, payments: 1000000 },
        ]);
      } catch (error) {
        console.error('Failed to fetch dashboard stats:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  if (loading) {
    return <div className="p-6 text-center">بارگذاری...</div>;
  }

  return (
    <div className="p-6 space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="کل کارمندان"
          value={stats.totalEmployees}
          icon="👥"
          trend="+2.5%"
        />
        <StatCard
          title="قرارداد فعال"
          value={stats.activeContracts}
          icon="📄"
          trend="+1.2%"
        />
        <StatCard
          title="پرداخت‌های در انتظار"
          value={stats.pendingPayments}
          icon="⏳"
          trend="-0.5%"
        />
        <StatCard
          title="حقوق ماهانهٔ متوسط"
          value={`${(stats.monthlyPayroll / 1000000).toFixed(1)}M`}
          icon="💰"
          trend="+3.2%"
        />
      </div>

      <div className="bg-white rounded-lg shadow-sm p-6">
        <h2 className="text-xl font-bold text-gray-900 mb-6">تمایل حقوق و پرداخت (4 ماهٔ اخیر)</h2>
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={chartData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="month" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Bar dataKey="payroll" fill="#1e3a8a" name="حقوق" />
            <Bar dataKey="payments" fill="#059669" name="پرداخت‌ها" />
          </BarChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}

export default Dashboard;
