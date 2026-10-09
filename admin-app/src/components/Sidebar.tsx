import { Link, useLocation } from 'react-router-dom';
import clsx from 'clsx';

const navigation = [
  { name: 'داشبورد', href: '/', icon: '📊' },
  { name: 'کارمندان', href: '/employees', icon: '👥' },
  { name: 'قرارداد', href: '/contracts', icon: '📄' },
  { name: 'حقوق', href: '/payroll', icon: '💰' },
  { name: 'گزارش‌ها', href: '/reports', icon: '📈' },
  { name: 'پرداخت‌ها', href: '/payments', icon: '💳' },
  { name: 'گزارش فعالیت', href: '/audit', icon: '🔍' },
];

function Sidebar() {
  const location = useLocation();

  return (
    <div className="w-64 bg-primary text-white shadow-lg flex flex-col">
      <div className="px-6 py-8 border-b border-primary-dark">
        <h1 className="text-2xl font-bold">PCM</h1>
        <p className="text-sm text-primary-light mt-1">مدیریت قرارداد کارمندان</p>
      </div>
      <nav className="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        {navigation.map((item) => {
          const isActive = location.pathname === item.href;
          return (
            <Link
              key={item.name}
              to={item.href}
              className={clsx(
                'flex items-center px-4 py-3 rounded-lg transition-colors',
                isActive
                  ? 'bg-white text-primary font-medium'
                  : 'text-white hover:bg-opacity-20 hover:bg-white'
              )}
            >
              <span className="ml-3 text-lg">{item.icon}</span>
              <span>{item.name}</span>
            </Link>
          );
        })}
      </nav>
      <div className="px-6 py-4 border-t border-primary-dark">
        <p className="text-xs text-primary-light">نسخهٔ 1.0.0</p>
      </div>
    </div>
  );
}

export default Sidebar;
