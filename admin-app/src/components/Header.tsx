import { useAuthStore } from '@/stores/authStore';

function Header() {
  const { user, logout } = useAuthStore();

  return (
    <header className="bg-white shadow-sm border-b border-gray-200">
      <div className="px-6 py-4 flex items-center justify-between">
        <div>
          <h2 className="text-lg font-medium text-gray-900">خوش‌آمدید</h2>
        </div>
        <div className="flex items-center space-x-4">
          <div className="text-right">
            <p className="font-medium text-gray-900">{user?.username}</p>
            <p className="text-sm text-gray-500">{user?.email}</p>
          </div>
          <button
            onClick={logout}
            className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            خروج
          </button>
        </div>
      </div>
    </header>
  );
}

export default Header;
