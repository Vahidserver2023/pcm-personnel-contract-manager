interface StatCardProps {
  title: string;
  value: string | number;
  icon: string;
  trend?: string;
}

function StatCard({ title, value, icon, trend }: StatCardProps) {
  return (
    <div className="bg-white rounded-lg shadow-sm p-6 border-l-4 border-primary">
      <div className="flex items-start justify-between">
        <div>
          <p className="text-gray-500 text-sm font-medium">{title}</p>
          <p className="text-2xl font-bold text-gray-900 mt-2">{value}</p>
          {trend && <p className="text-xs text-green-600 mt-2">{trend} از ماهٔ قبل</p>}
        </div>
        <div className="text-3xl">{icon}</div>
      </div>
    </div>
  );
}

export default StatCard;
